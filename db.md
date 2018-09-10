## task 表结构

| 字段        | 类型      | 注释     |
| ----------- | --------- | -------- |
| task_id     | int       | 任务id   |
| start_time  | date_time | 开始时间 |
| circle      | int       | 周期     |
| command     | varchar   | 脚本命令 |
| timeout     | int       | 超时时间 |
| create_time | int       | 创建时间 |
| status      | tinyint   | 状态     |



## task_log 表

| 字段       | 类型      | 注释         |
| ---------- | --------- | ------------ |
| log_id     | int       | 日志id       |
| start_time | date_time | 执行开始时间 |
| end_time   | date_time | 结束时间     |
| task_id    | int       | 任务id       |
| ip         | int       | IP地址       |

