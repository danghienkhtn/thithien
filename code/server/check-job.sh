#!/bin/bash

env=production
document_root=/usr/local/src/www/Portal/server/Job

 a=1
 
if ps ax | grep -v grep | grep server-portal-job-feed.php > /dev/null
then
    let a=1
else
    echo "server-portal-job-feed.php is not running"
    APPLICATION_ENV=$env $document_root/server-portal-job-feed.sh start
fi

if ps ax | grep -v grep | grep server-portal-job-feed-group-member.php > /dev/null
then
    let a=1
else
    echo "server-portal-job-feed-group-member.php is not running"
    APPLICATION_ENV=$env $document_root/server-portal-job-feed-group-member.sh start
fi


if ps ax | grep -v grep | grep server-portal-job-feed-user-tag.php > /dev/null
then
    let a=1
else
    echo "server-portal-job-feed-user-tag.php is not running"
    APPLICATION_ENV=$env $document_root/server-portal-job-feed-user-tag.sh start
fi



if ps ax | grep -v grep | grep server-portal-job-statistic-absence-history.php > /dev/null
then
    let a=1
else
    echo "server-portal-job-statistic-absence-history.php is not running"
    APPLICATION_ENV=$env $document_root/server-portal-job-statistic-absence-history.sh start
fi


if ps ax | grep -v grep | grep server-portal-job-expense.php > /dev/null
then
    let a=1
else
    echo "server-portal-job-expense.php is not running"
    APPLICATION_ENV=$env $document_root/server-portal-job-expense.sh start
fi


if ps ax | grep -v grep | grep server-portal-job-invite.php > /dev/null
then
    let a=1
else
    echo "server-portal-job-invite.php is not running"
    APPLICATION_ENV=$env $document_root/server-portal-job-invite.sh start
fi


if ps ax | grep -v grep | grep server-portal-job-absence-attendance.php > /dev/null
then
    let a=1
else
    echo "server-portal-job-absence-attendance.php is not running"
    APPLICATION_ENV=$env $document_root/server-portal-job-absence-attendance.sh start
fi


if ps ax | grep -v grep | grep server-portal-job-group-member.php > /dev/null
then
    let a=1
else
    echo "server-portal-job-group-member.php is not running"
    APPLICATION_ENV=$env $document_root/server-portal-job-group-member.sh start
fi


if ps ax | grep -v grep | grep server-portal-job-notification.php > /dev/null
then
    let a=1
else
    echo "server-portal-job-notification.php is not running"
   APPLICATION_ENV=$env $document_root/server-portal-job-notification.sh start
fi


if ps ax | grep -v grep | grep server-portal-job-useractive.php > /dev/null
then
    let a=1
else
    echo "server-portal-job-useractive.php is not running"
   APPLICATION_ENV=$env $document_root/server-portal-job-useractive.sh start
fi


if ps ax | grep -v grep | grep server-portal-job-statistic.php  > /dev/null
then
    let a=1
else
    echo "server-portal-job-statistic.php  is not running"
   APPLICATION_ENV=$env $document_root/server-portal-job-statistic.sh start
fi


if ps ax | grep -v grep | grep server-portal-job-absence.php  > /dev/null
then
    let a=1
else
    echo "server-portal-job-absence.php  is not running"
   APPLICATION_ENV=$env $document_root/server-portal-job-absence.sh start
fi


if ps ax | grep -v grep | grep server-portal-job-attendance-statistic.php > /dev/null
then
    let a=1
else
    echo "server-portal-job-attendance-statistic.php is not running"
    APPLICATION_ENV=$env $document_root/server-portal-job-attendance-statistic.sh start
fi


if ps ax | grep -v grep | grep server-portal-job-account-info.php > /dev/null
then
    let a=1
else
    echo "server-portal-job-account-info.php is not running"
    APPLICATION_ENV=$env $document_root/server-portal-job-account-info.sh start
fi


if ps ax | grep -v grep | grep server-portal-job-group.php  > /dev/null
then
    let a=1
else
    echo "server-portal-job-group.php  is not running"
   APPLICATION_ENV=$env $document_root/server-portal-job-group.sh start
fi

if ps ax | grep -v grep | grep server-portal-job-euro.php  > /dev/null
then
    let a=1
else
    echo "server-portal-job-euro.php  is not running"
   APPLICATION_ENV=$env $document_root/server-portal-job-euro.sh start
fi

exit 0