import importlib.util
import json
from pathlib import Path
import tempfile
import unittest

import shapefile

ROOT = Path(__file__).resolve().parents[1]
spec = importlib.util.spec_from_file_location('poligonos', ROOT / 'scripts/actualizar_poligonos.py')
mod = importlib.util.module_from_spec(spec)
spec.loader.exec_module(mod)


class ConversionTests(unittest.TestCase):
    def setUp(self):
        self.temp = tempfile.TemporaryDirectory()
        self.addCleanup(self.temp.cleanup)
        self.path = Path(self.temp.name)
        self.anterior = self.path / 'anterior.json'
        self.salida = self.path / 'salida.json'
        self.anterior.write_text('{"type":"FeatureCollection","features":[]}', encoding='utf-8')

    def crear(self, x=780000, y=2466000):
        name = self.path / 'predios'
        with shapefile.Writer(str(name), shapeType=5) as writer:
            writer.field('clave', 'C')
            writer.poly([[(x,y),(x,y+20),(x+20,y+20),(x+20,y),(x,y)]])
            writer.record('ignorar_dbf')
        return name.with_suffix('.shp')

    def test_conversion_y_recuperacion_sin_dbf(self):
        entrada = self.crear()
        entrada.with_suffix('.dbf').unlink()
        entrada.with_suffix('.shx').unlink()
        resultado = mod.convertir_shp(entrada, self.anterior, self.salida)
        self.assertEqual(resultado['poligonos'], 1)
        data = json.loads(self.salida.read_text(encoding='utf-8'))
        data['features'][0]['properties'] = {'CVE_CAT_OR': '070001'}
        self.anterior.write_text(json.dumps(data), encoding='utf-8')
        resultado = mod.convertir_shp(entrada, self.anterior, self.salida)
        self.assertEqual(resultado['claves_recuperadas'], 1)

    def test_rechaza_truncado_sin_modificar_salida(self):
        entrada = self.crear()
        entrada.write_bytes(entrada.read_bytes()[:-10])
        self.salida.write_text('vigente')
        with self.assertRaises(ValueError):
            mod.convertir_shp(entrada, self.anterior, self.salida)
        self.assertEqual(self.salida.read_text(), 'vigente')

    def test_rechaza_otro_municipio(self):
        with self.assertRaises(ValueError):
            mod.convertir_shp(self.crear(500000, 1000000), self.anterior, self.salida)
        self.assertFalse(self.salida.exists())

    def test_rechaza_archivo_falso(self):
        entrada = self.path / 'falso.shp'
        entrada.write_bytes(b'x' * 100)
        with self.assertRaises(ValueError):
            mod.convertir_shp(entrada, self.anterior, self.salida)

    def test_claves_ambiguas_no_se_recuperan(self):
        entrada = self.crear()
        mod.convertir_shp(entrada, self.anterior, self.salida)
        data = json.loads(self.salida.read_text(encoding='utf-8'))
        feature = data['features'][0]
        data['features'] = [dict(feature, properties={'CVE_CAT_OR': key}) for key in ['1','2','1']]
        self.anterior.write_text(json.dumps(data), encoding='utf-8')
        self.assertEqual(mod.convertir_shp(entrada, self.anterior, self.salida)['claves_recuperadas'], 0)


if __name__ == '__main__':
    unittest.main()
