SELECT 
    att.absen_id,
    COALESCE(p.nama_lengkap, '-') AS nama,
    COALESCE(MAX(CASE WHEN att.status = 0 THEN TO_CHAR(att.tanggal_absen, 'HH24:MI:SS') END), NULL) AS masuk,
    COALESCE(MAX(CASE WHEN att.status = 1 THEN TO_CHAR(att.tanggal_absen, 'HH24:MI:SS') END), NULL) AS pulang,
    COALESCE(EXTRACT(EPOCH FROM (MAX(CASE WHEN att.status = 1 THEN att.tanggal_absen END) - 
                                          MAX(CASE WHEN att.status = 0 THEN att.tanggal_absen END))), 0) / 3600 AS jam_kerja
FROM 
    attendancelog att
LEFT JOIN 
    pegawai p ON att.absen_id = p.absen_pegawai_id
LEFT JOIN 
    mesin m ON m.ipadress = att.ipmesin
WHERE 
    att.tanggal_absen::date = '2024-11-01' 
GROUP BY 
    att.absen_id, att.verified, m.ipadress, m.namamesin, p.nama_lengkap, p.departemen, p.nomorpin
ORDER BY 
    p.nama_lengkap ASC;
