"""HTTP cart regression: changes only this test's session, never creates orders."""
import http.cookiejar
import json
import re
import subprocess
import urllib.error
import urllib.parse
import urllib.request

PHP = r'D:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe'
product = json.loads(subprocess.check_output([PHP], input=b'''<?php
require 'Model/pdo.php';
echo json_encode(pdo_get_connection()->query('SELECT id_sp,gia_sp,so_luong FROM san_pham WHERE trang_thai=1 AND so_luong>=3 LIMIT 1')->fetch(PDO::FETCH_ASSOC));
'''))
assert product, 'Need an active product with stock >= 3'
base = 'http://localhost/dochoi/index.php?act='
client = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))

def call(action, data=None):
    request = urllib.request.Request(base + action, None if data is None else urllib.parse.urlencode(data).encode())
    try:
        response = client.open(request)
    except urllib.error.HTTPError as error:
        response = error
    return response.status, response.read().decode('utf-8')

call('add_to_cart', {'add_to_cart': '1', 'id_sp': product['id_sp'], 'quantity': '1'})
status, html = call('cart')
assert status == 200 and 'data-cart-quantity' in html and 'js/cart.js' in html
token = re.search(r'data-csrf="([^"]+)"', html).group(1)
key = 'product_' + str(product['id_sp'])
checks = 1

def update(expected=200, **values):
    global checks
    data = {'csrf_token': token, 'key': key, **values}
    status, body = call('cart_update', data)
    assert status == expected, (status, body)
    result = json.loads(body)
    assert result['success'] == (expected == 200), result
    checks += 1
    return result

result = update(quantity='3', price='1')
assert result['count'] == 3 and result['total'] == float(product['gia_sp']) * 3
assert result['items'][0]['quantity'] == 3
assert 'data-quantity="3"' in call('cart')[1]
for quantity in ['-1', '0', '1.5', 'abc', '2147483648', str(int(product['so_luong']) + 1)]:
    result = update(422, quantity=quantity)
    assert result['count'] == 3
update(403, quantity='2', csrf_token='wrong')
update(422, quantity='2', key='missing')
assert update(operation='state')['count'] == 3
assert call('cart_update')[0] == 405
assert update(quantity='2')['count'] == 2
assert update(operation='remove')['count'] == 0
assert update(operation='remove')['total'] == 0
assert update(operation='state')['items'] == []
print(f'Cart AJAX: {checks + 3} HTTP and persistence checks passed.')
