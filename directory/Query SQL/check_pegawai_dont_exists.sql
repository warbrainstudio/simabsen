
    SELECT DISTINCT 
        attendancelog.absen_id
    FROM 
        attendancelog
    LEFT JOIN 
        pegawai ON attendancelog.absen_id = pegawai.absen_pegawai_id
    WHERE 
        pegawai.absen_pegawai_id IS NULL

