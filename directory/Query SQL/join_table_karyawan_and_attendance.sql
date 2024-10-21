SELECT 
    attendancelog.absen_id, 
    COALESCE(pegawai.nama_lengkap, 'NO NAME') AS nama,
    COALESCE(pegawai.departemen, 'UNKNOWN') AS departemen,  -- Optional: provide a default value if needed
    COALESCE(pegawai.nomorpin, 'UNKNOWN') AS no_pin,          -- Optional: provide a default value if needed
    attendancelog.tanggal_absen, 
    CASE 
        WHEN attendancelog.status = 0 THEN 'masuk'
        WHEN attendancelog.status = 1 THEN 'pulang'
        ELSE 'Unknown' -- Optional, in case there are other values
    END AS status,
    attendancelog.ipmesin 
FROM 
    attendancelog
LEFT JOIN 
    pegawai ON attendancelog.absen_id = pegawai.absen_pegawai_id
--WHERE COALESCE(pegawai.nama_lengkap, 'NO NAME') = 'NO NAME'
WHERE attendancelog.tanggal_absen::date = '2024-10-01'
ORDER BY 
    attendancelog.tanggal_absen ASC;
