SELECT 
    att.absen_id,
    m.ipadress, 
    m.namamesin, 
    COALESCE(p.nama_lengkap, '-') AS nama,
    COALESCE(NULLIF(p.departemen, ''), '-') AS dept,
    COALESCE(NULLIF(p.nomorpin, ''), '-') AS nopin,
    CASE WHEN att.verified = 1 THEN 'Finger' ELSE 'Input' END AS verifikasi,
    COALESCE(MAX(CASE WHEN att.status = 0 THEN att.tanggal_absen END), NULL) AS masuk,
    COALESCE(MAX(CASE WHEN att.status = 1 THEN att.tanggal_absen END), NULL) AS pulang,
    COALESCE(EXTRACT(EPOCH FROM (MAX(CASE WHEN att.status = 1 THEN att.tanggal_absen END) - 
                                          MAX(CASE WHEN att.status = 0 THEN att.tanggal_absen END))), 0) / 3600 AS jam_kerja
FROM 
    attendancelog att
LEFT JOIN 
    pegawai p ON att.absen_id = p.absen_pegawai_id
LEFT JOIN 
    mesin m ON m.ipadress = att.ipmesin
WHERE 
    att.tanggal_absen::date = '2024-09-13' 
GROUP BY 
    att.absen_id, att.verified, m.ipadress, m.namamesin, p.nama_lengkap, p.departemen, p.nomorpin
ORDER BY 
    att.absen_id;
