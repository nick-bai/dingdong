<?php
namespace app\controller;

use think\facade\Db;

class Index extends Base
{
    public function index()
    {
        $onlineKeFuNum = number_format(Db::name('kefu')->where('status', 1)->count());
        $serviceCustomerNum = number_format(Db::name('customer')->count());
        $totalChatTime = changeTimeType(Db::name('service_log')->sum('chat_time'));
        $todayServiceNum = number_format(Db::name('service_log')
            ->where('start_time', '>', date('Y-m-d') . '  00:00:00')
            ->where('start_time', '<', date('Y-m-d') . '  23:59:59')
            ->count());

        $days7 = [];
        for ($i = 7; $i > 0; $i--) {
            $days7[] = date('Y-m-d', strtotime('-' . $i . ' days'));
        }

        $start = $days7[0];
        $end = $days7[6] . ' 23:59:59';

        $sevenNum = $this->census($start, $end, $days7);

        return json(['code' => 0, 'data' => [
            'sevenDays' => $days7,
            'onlineKeFuNum' => $onlineKeFuNum,
            'serviceCustomerNum' => $serviceCustomerNum,
            'totalChatTime' => $totalChatTime,
            'todayServiceNum' => $todayServiceNum,
            'sevenNum' => array_values($sevenNum),
        ], 'msg' => 'success']);
    }

    private function census($start, $end, $days)
    {
        $sql = "SELECT DATE_FORMAT(start_time, '%Y-%m-%d') as create_time2,count(id) as s_num from df_service_log WHERE start_time > '"
            . $start . "' and start_time < '" . $end . "' GROUP BY create_time2;";

        $all = Db::query($sql);

        $num = [];
        foreach ($days as $vo) {
            $num[$vo] = 0;
        }

        foreach ($all as $vo) {
            if (isset($num[$vo['create_time2']])) {
                $num[$vo['create_time2']] = $vo['s_num'];
            }
        }

        return $num;
    }
}
