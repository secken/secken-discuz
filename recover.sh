#!/bin/bash +x

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

if [ ! -d $dist ];then
    mkdir "$dist"
fi
if [ ! -d "$dist/$to" ];then
    mkdir "$dist/$to"
fi
if [ ! -d "$dist/$to/yangcong" ];then
    mkdir "$dist/$to/yangcong"
fi

echo "clean..."
rm -rf "$dist/$to/yangcong/*"

for file in `find $src | grep -v $dist/$to`
do
    name=${file##*/}
    if [ ${name:0:1} = "." -o "${file##*.}" = "md" -o "${file##*.}" = "sh" ];then
        echo "skip $file"
        continue
    fi

    if [ "${file##*.}" = "php" -o "${file##*.}" = "htm" ];then
        echo $file
        iconv -f $from -t $to $file > "$dist/$to/yangcong/${file#*"$src/"}"
    elif [ -d $file ]; then
        mkdir "$dist/$to/yangcong/${file#*"$src/"}"
    else
        echo "-- $file"
        cp $file "$dist/$to/yangcong/${file#*"$src/"}"
    fi
done

echo "recover xml"
iconv -f UTF-8 -t GBK "$dist/$to/yangcong/discuz_plugin_yangcong_SC_UTF8.xml" > "$dist/$to/yangcong/discuz_plugin_yangcong.xml"
cp "$dist/$to/yangcong/discuz_plugin_yangcong.xml" "$dist/$to/yangcong/discuz_plugin_yangcong_SC_GBK.xml"

echo "recover language"

iconv -f "GBK" -t "UTF-8" "$dist/$to/yangcong/language.php" > "$dist/$to/yangcong/language.SC_UTF8.php"
cp "$dist/$to/yangcong/language.php" "$dist/$to/yangcong/language.GBK.php"

echo "recover done!"
echo "ziping..."

cd "$dist/$to"
zip -r "yangcong_dz.zip" "yangcong"

echo "done!!!"
