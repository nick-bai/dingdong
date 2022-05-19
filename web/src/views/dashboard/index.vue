<template>
  <div class="container">
    <ul class="moneyBox">
      <li>
        <div>
          <div class="moneyType">
            <p>在线客服数</p>
            <span>{{ onlineKeFuNum }}人</span>
          </div>
          <div class="moneyIconBox">
            <!-- <span data-v-2d6b4fde="" class="iconfont"></span> -->
          </div>
        </div>
      </li>
      <li>
        <div>
          <div class="moneyType">
            <p>累计服务人数</p>
            <span>{{ serviceCustomerNum }}人</span>
          </div>
          <div class="moneyIconBox">
            <!-- <span data-v-2d6b4fde="" class="iconfont"></span> -->
          </div>
        </div>
      </li>
      <li>
        <div>
          <div class="moneyType">
            <p>累计服务时长</p>
            <span>{{ totalChatTime }}</span>
          </div>
          <div class="moneyIconBox">
            <!-- <span data-v-2d6b4fde="" class="iconfont"></span> -->
          </div>
        </div>
      </li>
      <li>
        <div>
          <div class="moneyType">
            <p>今日服务人数</p>
            <span>{{ todayServiceNum }}人</span>
          </div>
          <div class="moneyIconBox">
            <!-- <span data-v-2d6b4fde="" class="iconfont"></span> -->
          </div>
        </div>
      </li>
    </ul>
    <div class="echartsBox">
      <h3>最近7天访客数量</h3>
      <div id="customerEcharts" style="height: 400px" />
    </div>
  </div>
</template>

<script>
import * as echarts from 'echarts'

export default {
  name: 'Home',
  data() {
    return {
      onlineKeFuNum: 0,
      serviceCustomerNum: 0,
      totalChatTime: '00:00:00',
      todayServiceNum: 0
    }
  },
  mounted() {
    this.keyEchartInit()
  },
  methods: {
    async keyEchartInit() {
      const res = await this.$api.api.home.get()
      this.onlineKeFuNum = res.data.onlineKeFuNum
      this.serviceCustomerNum = res.data.serviceCustomerNum
      this.totalChatTime = res.data.totalChatTime
      this.todayServiceNum = res.data.todayServiceNum

      var chartDom = document.getElementById('customerEcharts')
      var myChart = echarts.init(chartDom)
      var option
      option = {
        color: ['#34daac', '#ff525d'],
        xAxis: {
          show: true,
          type: 'category',
          data: res.data.sevenDays,
          axisLine: {
            show: false // 隐藏x轴线
          },
          axisTick: {
            lineStyle: {
              color: '#eee'
            }
          }
        },
        legend: {
          y: 'bottom'
        },
        grid: {
          containLabel: true,
          top: '20px',
          bottom: '40px',
          right: '40px',
          left: '40px'
        },
        yAxis: {
          type: 'value',
          show: true,
          axisLabel: {
            textStyle: {
              color: '#303133'
            }
          },
          axisLine: {
            // y轴
            lineStyle: {
              color: '#eee'
            }
          },
          splitLine: {
            lineStyle: {
              color: '#eee'
            }
          },
          axisTick: {
            // y轴刻度线
            show: false
          }
        },
        tooltip: {
          trigger: 'axis',
          axisPointer: {
            formatter: '{c} '
          }
        },
        series: [
          {
            name: '接待访客数',
            type: 'line',
            data: res.data.sevenNum,
            smooth: true
          }
        ]
      }

      option && myChart.setOption(option)
    }
  }
}
</script>

<style lang="scss" scoped>
#keywordsEchart {
  margin-top: 20px;

  width: 100%;
  height: calc(50vh - 150px);
}
.container {
  padding: 10px;
  // overflow-y: scroll;
  box-sizing: border-box;
  background: #f1f1f1;
}
* {
  margin: 0;
  padding: 0;
  font-family: number, Microsoft YaHei, iconfont_user;
}
ul {
  list-style: none;
}
.moneyBox {
  display: flex;
  justify-content: space-between;
  overflow: hidden;
  flex-wrap: wrap;
}
.moneyBox > li {
  width: 24%;
  padding-bottom: 20px;
  box-sizing: border-box;
}
.moneyBox > li:first-child > div {
  background: #409eff;
}
.moneyBox > li:nth-child(2) > div {
  background: #ed706d;
}
.moneyBox > li:nth-child(3) > div {
  background: #68dcf6;
}
.moneyBox > li:nth-child(4) > div {
  background: #1bc98e;
}
.moneyBox > li > div {
  border: 1px solid #e7e7e7;
  padding: 30px 86px 30px 30px;
  overflow: hidden;
  border-radius: 5px;
  position: relative;
  color: #fff;
}
.moneyIconBox {
  position: absolute;
  right: 30px;
  top: 30px;
  width: 56px;
  height: 56px;
  border-radius: 50%;
  overflow: hidden;
  text-align: center;
}
.moneyType {
  float: left;
  line-height: 28px;
}
.moneyType p {
  color: #fff;
  font-size: 20px;
  font-weight: 700;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  width: 170px;
}
.moneyType span {
  font-size: 16px;
}
.keywords {
  margin-bottom: 15px;
  display: flex;
  justify-content: space-between;
  overflow: hidden;
  flex-wrap: wrap;
}
.keywords-title {
  height: 50px;
  line-height: 50px;
  padding-left: 30px;
  border-bottom: 1px solid #ccc;
}
.keywords > li {
  width: 49.5%;
  border: 1px solid #e7e7e7;
  box-sizing: border-box;
  background: #fff;
  border-radius: 5px;
}
.enginelist {
  justify-content: space-between;
  display: flex;
  flex-wrap: wrap;
  overflow: hidden;
}
.enginelist > li {
  width: 33%;
  padding: 30px 40px;
}
.keylist {
  display: flex;
  flex-wrap: wrap;
}

.keylist > li {
  width: 25%;
  padding: 30px 40px;
}
.key-item {
  float: left;
  line-height: 24px;
}
.key-item p {
  font-size: 20px;
  margin-bottom: 15px;
}
.key-item span {
  font-size: 24px;
  font-weight: 700;
}
.echartsBox {
  border-radius: 5px;
  background: #fff;
  border: 1px solid #e7e7e7;
  padding: 30px 30px 0;
  height: 500px;
  width: 100%;
  box-sizing: border-box;
}
</style>
