<template>
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="widget-9 card  bg-primary no-margin widget-loader-bar">
                <div class="full-height d-flex flex-column">
                    <div class="card-header ">
                        <div class="card-title">
                            <span class="font-montserrat fs-11 all-caps">Number Of Users </span>
                        </div>
                        <div class="card-controls">
                            <ul>
                                <li><a href="#" class="card-refresh" data-toggle="refresh"><i
                                    class="card-icon card-icon-refresh"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="p-l-20">
                        <h3 class="no-margin p-b-5">$23,000</h3>
                        <span class="d-flex align-items-center">
					<i class="pg-icon m-r-5">arrow_down</i>
					<span class="small hint-text">65% lower than last month</span>
                          </span>
                    </div>
                    <div class="mt-auto">
                        <div class="progress progress-small m-b-20">
                            <div class="progress-bar progress-bar-white" style="width:45%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xs-12 col-sm-6">
            <div class="widget-10 card  bg-white no-margin widget-loader-bar">
                <div class="card-header  top-left top-right ">
                    <div class="card-title text-black hint-text">
                        <span class="font-montserrat fs-11 all-caps">Subscribed Users</span>
                    </div>
                    <div class="card-controls">
                        <ul>
                            <li><a data-toggle="refresh" class="card-refresh text-black" href="#"><i
                                class="card-icon card-icon-refresh"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body p-t-40">
                    <div class="row">
                        <div class="col-sm-12">
                            <h4 class="no-margin p-b-5 text-danger semi-bold">APPL 2.032</h4>
                            <div class="d-flex align-items-center pull-left small">
                                <span>WMHC</span>
                                <span class=" text-success"> <i class="pg-icon m-l-10">arrow_up</i> </span>
                                <span class="text-success font-montserrat"> 21% </span>
                            </div>
                            <div class="d-flex align-items-center pull-left m-l-20 small">
                                <span>HCRS</span>
                                <span class="text-danger"><i class="pg-icon m-l-10">arrow_down</i> </span>
                                <span class="text-danger font-montserrat"> 21% </span>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="p-t-5 full-width">
                        <a href="#" class="btn-circle-arrow b-grey"><i
                            class="pg-icon">chevron_down</i></a>
                        <span class="hint-text small">Show more</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xs-12 col-sm-6">
            <div class="widget-8 card  bg-success no-margin widget-loader-bar">
                <div class="container-xs-height full-height">
                    <div class="row-xs-height">
                        <div class="col-xs-height col-top">
                            <div class="card-header  top-left top-right">
                                <div class="card-title">
                                    <span class="font-montserrat fs-11 all-caps">From Subscriptions</span>
                                </div>
                                <div class="card-controls">
                                    <ul>
                                        <li>
                                            <a data-toggle="refresh" class="card-refresh" href="#"><i
                                                class="card-icon card-icon-refresh"></i></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row-xs-height ">
                        <div class="col-xs-height col-top relative">
                            <div class="row full-height">
                                <div class="col-sm-6">
                                    <div class="p-l-20 full-height d-flex flex-column justify-content-between">
                                        <h3 class="no-margin p-b-5">$14,000</h3>
                                        <p class="small m-t-5 m-b-20">
                                            <span
                                                class="label label-white hint-text font-montserrat m-r-5">60%</span><span
                                            class="fs-12">Higher</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "DashboardCards"
}
</script>

<style scoped>

</style>
