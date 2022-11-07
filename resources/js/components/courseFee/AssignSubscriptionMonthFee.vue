<template>
    <div>
        <div class="card card-borderless">
            <ul class="nav nav-tabs nav-tabs-simple" role="tablist" data-init-reponsive-tabs="dropdownfx">
                <li class="nav-item">
                    <a data-toggle="tab" role="tab" href="#"
                       @click.prevent="switchTab('MONTHLY_FEE')"
                       :class="{'active':activeTab==='MONTHLY_FEE'}">
                        Assign Monthly Fee
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" data-toggle="tab" role="tab"
                       @click.prevent="switchTab('YEARLY_FEE')"
                       :class="{'active':activeTab!=='MONTHLY_FEE'}">
                        Assign Yearly Fee
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <MonthlyFee :class-group-syllabus-id="classGroupSyllabusId" v-if="activeTab==='MONTHLY_FEE'"/>
                <AssignYearlyFee :class-group-syllabus-id="classGroupSyllabusId" v-else/>
            </div>
        </div>
    </div>
</template>

<script>

    import MonthlyFee from "./MonthlyFee";
    import AssignYearlyFee from "./AssignYearlyFee";

    export default {
        name: "AssignSubscriptionMontFee",
        components: {AssignYearlyFee, MonthlyFee},
        data() {
            return {
                classGroupSyllabusId: "",
                activeTab: "MONTHLY_FEE"
            }
        },
        mounted() {
            this.fetchDataFromRoutes();
        },
        methods: {
            fetchDataFromRoutes() {
                const route = this.$route;
                this.classGroupSyllabusId = route.params.classGroupSyllabusId;
            },
            switchTab(tab) {
                this.activeTab = tab;
            }
        }
    }
</script>

<style scoped>

</style>
