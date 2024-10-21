SELECT 
    attendancelog.userid, 
    'NO NAME' AS nama,
    'UNKNOWN' AS departemen,  -- Optional: provide a default value if needed
    'UNKNOWN' AS nomorpin,      -- Optional: provide a default value if needed
    attendancelog.datetime, 
    CASE 
        WHEN attendancelog.status = 0 THEN 'masuk'
        WHEN attendancelog.status = 1 THEN 'pulang'
        ELSE 'Unknown' -- Optional, in case there are other values
    END AS status,
    attendancelog.machine 
FROM 
    attendancelog
LEFT JOIN 
    karyawan ON attendancelog.userid = karyawan.userid
WHERE 
    karyawan.userid IS NULL 
--AND attendance_log.user_id = '741'
ORDER BY 
    attendancelog.datetime DESC;