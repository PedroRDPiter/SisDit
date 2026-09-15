"""Regenera la capa del mapa. Dependencias: pyshp==2.3.1 y pyproj.

El SHP municipal usa UTM zona 13 norte; el mapa requiere longitud/latitud.
Solo recupera atributos anteriores cuando coinciden todos los vértices.
"""
import argparse
import json
import struct
from pathlib import Path

import shapefile
from pyproj import Transformer

def vertices(coords):
    if not coords:
        return
    if isinstance(coords[0], (int, float)):
        yield tuple(round(v, 7) for v in coords[:2])
    else:
        for child in coords:
            yield from vertices(child)


def convertir_shp(entrada, anterior_path, salida):
    entrada = Path(entrada)
    with entrada.open('rb') as archivo:
        header = archivo.read(100)
    if len(header) != 100 or struct.unpack('>i', header[:4])[0] != 9994:
        raise ValueError('El archivo no es un SHP válido.')
    if struct.unpack('<i', header[28:32])[0] != 1000 or struct.unpack('<i', header[32:36])[0] not in (5, 15, 25):
        raise ValueError('El SHP debe contener polígonos catastrales.')
    if struct.unpack('>i', header[24:28])[0] * 2 != entrada.stat().st_size:
        raise ValueError('El archivo SHP está incompleto.')
    # Validar longitudes antes de que el lector reserve memoria para los puntos.
    with entrada.open('rb') as archivo:
        archivo.seek(100)
        while archivo.tell() < entrada.stat().st_size:
            record = archivo.read(8)
            if len(record) != 8:
                raise ValueError('Registro SHP incompleto.')
            size = struct.unpack('>i', record[4:])[0] * 2
            if size < 4 or size > entrada.stat().st_size - archivo.tell():
                raise ValueError('Longitud de registro SHP inválida.')
            data = archivo.read(size)
            tipo = struct.unpack('<i', data[:4])[0]
            if tipo == 0:
                continue
            if tipo not in (5, 15, 25) or size < 44:
                raise ValueError('Registro de polígono inválido.')
            parts, points = struct.unpack('<2i', data[36:44])
            if parts < 0 or points < 0 or parts > points or 44 + 4 * parts + 16 * points > size:
                raise ValueError('Los puntos del polígono están incompletos.')
            starts = struct.unpack('<' + str(parts) + 'i', data[44:44 + 4 * parts])
            if points and (not starts or starts[0] != 0 or starts[-1] >= points or any(a >= b for a, b in zip(starts, starts[1:]))):
                raise ValueError('Los anillos del polígono son inválidos.')
    anterior = json.loads(Path(anterior_path).read_text(encoding='utf-8-sig'))
    atributos = {}
    for feature in anterior['features']:
        if feature.get('geometry'):
            key = frozenset(vertices(feature['geometry']['coordinates']))
            props = feature.get('properties') or {}
            if key in atributos and atributos[key] != props:
                atributos[key] = None  # No asignar cuentas ambiguas.
            else:
                atributos[key] = props
    transformar = Transformer.from_crs('EPSG:32613', 'EPSG:4326', always_xy=True)

    def convertir(coords):
        if not coords:
            return []
        if isinstance(coords[0], (int, float)):
            lon, lat = transformar.transform(*coords[:2])
            if not (-103 < lon < -101 and 21 < lat < 23):
                raise ValueError('Coordenada fuera del municipio; revisar proyección.')
            return [lon, lat]
        return [convertir(child) for child in coords]

    features = []
    recuperados = vacios = 0
    with entrada.open('rb') as shp_file, shapefile.Reader(shp=shp_file) as source:
        for shape in source.iterShapes():
            if shape.shapeType not in (0, 5, 15, 25):
                raise ValueError('El archivo contiene geometrías que no son polígonos.')
            if len(set(tuple(point) for point in shape.points)) < 3:
                vacios += 1
                continue
            geometry = shape.__geo_interface__
            geometry['coordinates'] = convertir(geometry['coordinates'])
            rings = geometry['coordinates'] if geometry['type'] == 'Polygon' else [r for poly in geometry['coordinates'] for r in poly]
            if any(len(r) < 4 or r[0] != r[-1] for r in rings):
                raise ValueError('El SHP contiene anillos incompletos o degenerados.')
            props = atributos.get(frozenset(vertices(geometry['coordinates']))) or {}
            recuperados += bool(props.get('CVE_CAT_OR'))
            features.append({'type': 'Feature', 'properties': props, 'geometry': geometry})
    if not features:
        raise ValueError('El archivo no contiene polígonos utilizables.')
    output = {'type': 'FeatureCollection', 'features': features}
    Path(salida).write_text(
        json.dumps(output, ensure_ascii=False, separators=(',', ':'), allow_nan=False), encoding='utf-8')
    return {'poligonos': len(features), 'claves_recuperadas': recuperados, 'sin_clave': len(features) - recuperados, 'omitidos': vacios}


if __name__ == '__main__':
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--entrada', required=True)
    parser.add_argument('--anterior', required=True)
    parser.add_argument('--salida', required=True)
    args = parser.parse_args()
    try:
        print(json.dumps({'success': True, **convertir_shp(args.entrada, args.anterior, args.salida)}))
    except Exception as error:
        print(json.dumps({'success': False, 'message': str(error)}))
        raise SystemExit(1)
