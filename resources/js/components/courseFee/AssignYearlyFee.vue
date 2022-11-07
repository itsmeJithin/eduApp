<template>
    <div class="tab-pane active" id="annual-fee">
        <div class="row">
            <div class="col-lg-12">
                <p class="hint-text">
                    You can assign fee for an year so the students can pay the given amount to purchase whole year
                    or you can enter the discount percentage so we will calculate the yearly amount by subtracting the
                    discount percentage from total monthly fee if you configured monthly fee.
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-sm-12 col-md-6">
                <label for="amount">Enter the Annual Fee</label>
                <div class="form-group input-group transparent">
                    <div class="input-group-prepend">
                                        <span class="input-group-text transparent">
                                            &#x20B9;
							            </span>
                    </div>
                    <input id="amount" type="text" placeholder="Example: 3500" class="form-control"
                           v-model="annualFee"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-sm-12 col-md-6 text-center">
                <h3>OR</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-sm-12 col-md-6">
                <label for="discount">Enter the discount in percentage</label>
                <div class="form-group input-group transparent">
                    <div class="input-group-prepend">
                                        <span class="input-group-text transparent">
                                            %
							            </span>
                    </div>
                    <input id="discount" type="text" placeholder="Example: 10" class="form-control" v-model="discount"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-right mt-2">
                <button aria-label="" type="button" class="btn btn-default btn-cons" @click.prevent="reset">
                    <i class="fa fa-times mr-1"></i>Reset
                </button>
                <button aria-label="" type="button" class="btn btn-success btn-cons" @click.prevent="saveAnnualFee">
                    <i class="fa fa-check mr-1"></i>Save
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import CourseFeeAPI from "./CourseFeeAPI";

    export default {
        name: "AssignYearlyFee",
        props: {
            classGroupSyllabusId: {
                required: true,
                type: String
            }
        },
        data() {
            return {
                annualFee: "",
                discount: "",
                courseFee: null,
                totalMonthlyFee: ""
            }
        },
        mounted() {
            if (this.classGroupSyllabusId)
                this.getAnnualFee()
        },
        watch: {
            annualFee() {
                if (this.annualFee)
                    this.discount = "";
            },
            discount() {
                if (this.discount)
                    this.annualFee = "";
            }
        },
        methods: {
            reset() {
                if (this.courseFee) {
                    if (this.courseFee.price) {
                        this.annualFee = this.courseFee.price;
                        this.discount = "";
                    } else {
                        this.annualFee = "";
                        this.discount = this.courseFee.discount;
                    }
                } else {
                    this.annualFee = this.totalMonthlyFee;
                    this.discount = "";
                }

            },
            getAnnualFee() {
                $('#annual-fee').block();
                this.$axios.get(CourseFeeAPI.GET_ANNUAL_FEE, {
                    params: {
                        class_group_syllabus_id: this.classGroupSyllabusId
                    }
                })
                    .then(response => {
                        $('#annual-fee').unblock();
                        const responseData = response.data;
                        if (responseData.success) {
                            this.courseFee = responseData.data.annualFee;
                            this.totalMonthlyFee = responseData.data.totalAssignedFee;
                            const fee = responseData.data.annualFee;
                            if (fee) {
                                if (fee.price) {
                                    this.annualFee = fee.price;
                                } else {
                                    this.discount = fee.discount;
                                }
                            } else {
                                this.annualFee = responseData.data.totalAssignedFee
                            }

                        } else
                            $.showNotification(responseData.error, "error");
                    })
                    .catch(() => {
                        $('#annual-fee').unblock();
                        $.showNotification("Error occurred while fetching the subscription months", "error");
                    })
            },
            saveAnnualFee() {
                $('#annual-fee').block();
                const data = {
                    class_group_syllabus_id: this.classGroupSyllabusId,
                    price: this.annualFee,
                    discount: this.discount
                };
                this.$axios.post(CourseFeeAPI.SAVE_ANNUAL_FEE, data)
                    .then(response => {
                        $('#annual-fee').unblock();
                        const responseData = response.data;
                        if (responseData.success) {
                            $.showNotification(responseData.message);
                        } else {
                            $.showNotification(responseData.error, "error");
                        }
                    })
                    .catch(() => {
                        $('#annual-fee').unblock();
                        $.showNotification("Error occurred while saving the fee. Try again later", "error");
                    })

            },
        }
    }
</script>

<style scoped>

</style>
