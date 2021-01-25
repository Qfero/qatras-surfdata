<?php

namespace Qatras\Surfdata\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class VisitTrack extends Model
{
    use HasFactory;

    protected $table = 'visittracks';
    public $timestamps = false;
    protected $fillable = [
        'visitor_ip',
        'visitor_country',
        'visitor_region',
        'visitor_city',
        'visitor_zip',
        'visitor_latitude',
        'visitor_longitude',
        'visitor_timezone',
        'visitor_isp',
        'visitor_org',
        'visitor_as',
        'visitor_query',
        'visitor_userAgent',
        'visitor_userAgentPattern',
        'visitor_browser',
        'visitor_browserVersion',
        'visitor_platform',
        'visitor_platformVersion',
        'visitor_date',
        'visitor_day',
        'visitor_month',
        'visitor_year',
        'visitor_hour',
        'visitor_minute',
        'visitor_seconds',
        'visitor_referer',
        'visitor_page',
        'visitor_closed'
    ];

}

