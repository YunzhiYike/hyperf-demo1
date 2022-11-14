#!/bin/bash
#
echo -e "\033[32m 1.删除停止的容器 \033[0m"
docker rm `docker ps -a|grep Exited|awk '{print $1}'`
echo -e "\033[32m 2.删除所有docker镜像 \033[0m"
sudo docker rmi $(docker images -q)
echo -e "\033[32m 3.执行docker-compose \033[0m"
docker-compose up -d
echo -e "\033[32m 4.删除缓存 \033[0m"
for i in `ls /www/wwwroot/logs/`;
do
 rm -rf /www/wwwroot/logs/$i/container;
done
echo -e "\033[32m 5.【服务部署完成 SUCCESS】 \033[0m"
