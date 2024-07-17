#!/bin/bash

while true; do
    rsync -avz -e "ssh -i /home/ubuntu/vockey.pem" /home/ubuntu/docker-lamp/administrador/images/ ubuntu@ec2-44-215-247-51.compute-1.amazonaws.com:/home/ubuntu/docker-lamp/administrador/images/
    rsync -avz -e "ssh -i /home/ubuntu/vockey.pem" ubuntu@ec2-44-215-247-51.compute-1.amazonaws.com:/home/ubuntu/docker-lamp/administrador/images/ /home/ubuntu/docker-lamp/administrador/images/
    sleep 1
done
