# Apotek Digital

## Pendahuluan
project ini merupakan testing untuk implement ke real case sebuah masalah. Dengan adanya manajemen obat oleh kasir secara digital dapat mempermudah untuk menghitung stok, atau pun menghitung keuntngan.

## User & Role
di project ini tersedia 1 role super admin dan juga banyak kasir.

Super admin tersedia di database seeder,
```
Email: chaezaibnuakbar@gmail.com
Password: password123
```

## Point
di project ini tersedia point bagi member yang terdaftar, 
setiap transaksi yang dilakukan melebihi 20.000 maka akan menambah 15 hari expired.

Adapun jika transaksi yang dilakukan merupakan kelipatan 25.000 maka

```
25.000 -> point bertambah 10
50.000 -> point bertambah 22
75.000 -> point bertambah 36
100.000 -> oint bertambah 52
```

dan terus berlanjut kelipatannya.

Jika Transaksi = 100.000
Kelipatan = 4
```
Poin:
1 → 10 poin
2 → 12 poin
3 → 14 poin
4 → 16 poin
```

Total: **10 + 12 + 14 + 16 = 52 poin**

Jika Transaksi = 150.000
Kelipatan = 6

Poin: **10 + 12 + 14 + 16 + 18 + 20 = 90 poin**
