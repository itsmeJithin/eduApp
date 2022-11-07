<template>
    <SliderModal :show-slider="showSlider" :options="options" @on-close="onClose">
        <template slot="modal-header">
            <h4 class="mt--2rem">Question Details</h4>
        </template>
        <template slot="modal-content" v-if="question">
            <div class="row mt-3">
                <div class="col-md-12">
                    <label class="colon-after font-weight-bold">Question</label>
                    <span v-html="question.question"></span>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <label class="font-weight-bold colon-after">Options</label>
                </div>
            </div>
            <div class="row mt-2" v-for="(option,index) in question.options">
                <div class="col-md-1 pr-0">
                    {{ index + 1 }}.
                </div>
                <div class="col-md-11 pl-0" :class="{'text-success':option.isRightAnswer}" v-html="option.answer">
                </div>
            </div>
            <hr class="mt-1 mb-1">
            <div class="row mt-2">
                <div class="col-md-2 colon-after">
                    <label>Mark</label>
                </div>
                <div class="col-md-10">
                    {{ question.mark }}
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-2 colon-after">
                    <label>Time</label>
                </div>
                <div class="col-md-10 ml-0">
                    {{ question.question_time ? question.question_time : "-" }}
                </div>
            </div>
        </template>
        <template slot="modal-footer">
            <button type="button" class="btn btn-default btn-lg btn-xs-block"
                    @click.prevent="onClose()">
                <i class="fa fa-times mr-1"></i>Close
            </button>
            <button type="button" class="btn btn-success btn-lg btn-xs-block" v-if="showAssignButton"
                    @click.prevent="onAssign()">
                <i class="fa fa-plus mr-1"></i>Assign
            </button>
        </template>
    </SliderModal>
</template>

<script>
import SliderModal from "../common/SliderModal";

export default {
    name: "ViewQuestionDetails",
    components: {SliderModal},
    props: {
        question: {
            required: true,
        },
        showSlider: {
            required: true,
            type: Boolean,
            default: false
        },
        showAssignButton: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            options: {
                position: "right",
                size: "large",
            },
        }
    },
    methods: {
        onClose() {
            this.$emit("on-close");
        },
        onAssign() {
            this.$emit("on-assign", this.question);
            this.$emit("on-close");
        }
    }
}
</script>

<style scoped>

</style>
