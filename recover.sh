#!/bin/bash

# 编码转换工具
# -f 原编码
# -t 转换编码
# -i 原文件夹
# -o 生成文件夹
# 默认值如下，凑合用吧

from="UTF-8"
to="GBK"
src="."
dist="."

while getopts :f:t:i:o: opt; do
    case $opt in
        f) from=$OPTARG ;;
        t) to=$OPTARG ;;
        i) src=$OPTARG ;;
        o) dist=$OPTARG ;;
        *) src=$OPTARG ;;
    esac
done

eval `mkdir $dist/$to/`

for file in `find $src`
do
    name=${file##*/}
    if [ ${name:0:1} = "." ];then
        echo "skip $file"
        continue
    fi

    if [ "${file##*.}" = "php" -o "${file##*.}" = "htm" ];then
        echo $file
        iconv -f $from -t $to $file > "$dist/$to/${file#*"$src/"}"
    elif [ -d $file ]; then
        mkdir "$dist/$to/${file#*"$src/"}"
    else
        echo "-- $file"
        cp $file "$dist/$to/${file#*"$src/"}"
    fi
done

echo "recover xml"
eval `iconv -f UTF-8 -t GBK $dist/$to/discuz_plugin_yangcong_SC_UTF8.xml > $dist/$to/discuz_plugin_yangcong.xml`
