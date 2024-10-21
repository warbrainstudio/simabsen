SELECT 
    COALESCE(u.nama_unit, 'Tanpa Departemen') AS dept,
    COUNT(p.departemen) AS dep_count,
    u.id,
    u.nama_unit
FROM pegawai p
LEFT JOIN unit u ON p.departemen = u.nama_unit
GROUP BY u.id, u.nama_unit
HAVING u.nama_unit IS NULL OR u.nama_unit = '';
