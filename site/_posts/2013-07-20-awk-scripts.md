---
layout: post
type: programming
title: awk 日志统计脚本
date: 2013/07/20 18:30:00 +0800
permalink: /page/awk-scripts.html
tags:
- awk
- shell
---

使用 awk 统计日志中各项平均耗时

### 基础扫描版

扫描整个日志，从 Timer 字段开始定位，过滤掉无用的 tbapi 开头的 timer

```
less ui.log | 
awk 'BEGIN{FS="Timer\\["}{print $2}' | 
awk 'BEGIN{FS="\\]"}{print $1}' | 
awk '{for(i=1;i<=NF;i++){if($i!~/tbapi/){print $i}}}' | 
awk 'BEGIN{FS=":"}{c[$1]++;s[$1]+=$2;}END{for(i in s){printf("%.2f\t%d\t%s\n",s[i]/c[i]/1000,c[i],i);}}' | 
sort -n
```

### 定时刷新版 

每 2s 自动 tail 500 条，原理同上

（ps：符号转义好恶心啊）

```
set +o history
watch "tail -500 ui.log|awk 'BEGIN{FS=\"Timer\\\[\"}{print \$2}'|awk 'BEGIN{FS=\"\\\]\"}{print \$1}'|awk '{for(i=1;i<=NF;i++){if(\$i!~/tbapi/){print \$i}}}'|awk 'BEGIN{FS=\":\"}{c[\$1]++;s[\$1]+=\$2;}END{for(i in s){printf(\"%.2f\t%d\t%s\n\",s[i]/c[i]/1000,c[i],i);}}'|sort -r -n"  
```
