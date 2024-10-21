WITH MissingUsers AS (
    SELECT DISTINCT 
        attendance_log.user_id
    FROM 
        attendance_log
    LEFT JOIN 
        karyawan ON attendance_log.user_id = karyawan.user_id
    WHERE 
        karyawan.user_id IS NULL
)
SELECT 
    attendance_log.user_id,
    COUNT(attendance_log.datetime) AS datetime_count
FROM 
    attendance_log
INNER JOIN 
    MissingUsers ON attendance_log.user_id = MissingUsers.user_id
GROUP BY 
    attendance_log.user_id
ORDER BY 
    attendance_log.user_id;
