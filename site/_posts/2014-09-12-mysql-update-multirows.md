---
layout: post
type: legacy
title: MySQL 更新多条记录的不同值
date: 2014/09/12 22:23:00 +0800
permalink: /page/mysql-update-multirows.html
tags:
- MySQL
- SQL
---

MySQL语句使用过程中，会用到一些多行更新的场景。比较常见的是，把多行都更新成同一个值。如：

```sql
UPDATE table_name  
SET column_value=1  
WHERE column_id IN (1,2,3);
```

如果更新多行数据，且值是不同的，往往会使用for循环进行更新。如：

```php
foreach ($data_list as $item) {  
    $sql = "UPDATE table_name SET column={$item['value']} WHERE column={$item['id']}";  
    $ret = mysql_query($sql);  
    if ($ret === false) {  
        //...
    }  
}  
```

这样写网络交互次数比较多，可能会导致性能问题，看起来也不优雅。
那能否一条SQL搞定呢？答案是肯定的。

```sql
UPDATE table_name  
SET column_value = CASE column_id  
    WHEN 1 THEN 0  
    WHEN 2 THEN 1  
    WHEN 3 THEN 2  
WHERE column_id IN(1,2,3);
```

同理，也可以使用此方法设置多行多个值。

```sql
UPDATE table_name  
SET column_value = CASE column_id  
    WHEN 1 THEN 0  
    WHEN 2 THEN 1  
    WHEN 3 THEN 2,  
SET column_desc = CASE column_id  
    WHEN 1 THEN 'test 1'
    WHEN 2 THEN 'test 2'
    WHEN 3 THEN 'test 3'
WHERE column_id IN(1,2,3);
```

END.
