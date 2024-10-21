WITH UserDateHours AS (
    SELECT 
        user_id, 
        DATE(datetime) AS log_date,
        EXTRACT(HOUR FROM datetime) AS hour_part
    FROM attendance_log
    WHERE status = 0
    GROUP BY user_id, log_date, hour_part
    HAVING COUNT(*) > 1
),

UserDateWithDifferentHours AS (
    SELECT 
        user_id, 
        log_date,
        COUNT(DISTINCT hour_part) AS distinct_hour_count
    FROM UserDateHours
    GROUP BY user_id, log_date
    HAVING COUNT(DISTINCT hour_part) > 1
)

SELECT 
    al.user_id, 
    karyawan.nama,
    karyawan.departemen,
    karyawan.no_pin,
    al.datetime, 
    al.verified, 
    CASE 
        WHEN al.status = 0 THEN 'masuk'
        WHEN al.status = 1 THEN 'pulang'
        ELSE 'Unknown' -- Optional, in case there are other values
    END AS status,
    al.machine,
    udh.distinct_hour_count -- Count of different hours for the user on this date
FROM 
    attendance_log al
JOIN 
    karyawan ON al.user_id = karyawan.user_id
JOIN 
    UserDateWithDifferentHours udh
ON 
    al.user_id = udh.user_id
    AND DATE(al.datetime) = udh.log_date
WHERE 
    al.status = 0 AND
	al.user_id = 291
ORDER BY 
    al.user_id, 
    DATE(al.datetime), 
    EXTRACT(HOUR FROM al.datetime), 
    al.datetime ASC;
	--distinct_hour_count DESC;
