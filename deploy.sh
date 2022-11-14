#!/bin/bash

# 删除停止的容器
docker rm `docker ps -a|grep Exited|awk '{print $1}'`
# 删除所有镜像
sudo docker rmi $(docker images -q)
# 执行docker-compose
docker-compose up -d
# 删除缓存
for i in `ls /www/wwwroot/logs/`;
do
 rm -rf /www/wwwroot/logs/$i/container;
done
