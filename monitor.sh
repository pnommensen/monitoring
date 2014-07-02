#!/bin/bash
wget -q https://raw.githubusercontent.com/jsdelivr/monitoring/master/nodes.txt
while read site
do
        CURL=$(curl -m 50 -s --head --output /dev/null $site)

done < nodes.txt
rm -rf nodes.txt
