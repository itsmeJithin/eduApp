<template>
    <SliderModal id="answer-doubts-modal" :show-slider="showSlider" @on-close="onClose">
        <template slot="modal-header">
            <h4 class="mt-0">Answer Doubts</h4>
        </template>
        <template slot="modal-content">
            <form action="" role="form">
                <div class="form-group" :class="{ 'has-error': $v.answer.$error && $v.answer.$error }">
                    <label>Answer</label>
                    <textarea type="text" class="form-control" required name="className" id="class_name"
                              v-model.trim="$v.answer.$model">
                    </textarea>
                </div>
                <label class="error" role="alert" for="class_name"
                       v-if="$v.answer.$error && !$v.answer.required">
                    Enter a valid answer
                </label>
                <label class="error" role="alert" for="class_name"
                       v-if="$v.answer.$error && !$v.answer.minLength">
                    Answer must have at least {{ $v.answer.$params.minLength.min }}
                    letters.
                </label>
            </form>
        </template>
        <template slot="modal-footer">
            <button type="button" class="btn btn-default btn-lg btn-xs-block"
                    @click.prevent="onClose()">
                Cancel
            </button>
            <button type="button" class="btn btn-primary btn-lg btn-xs-block"
                    @click.prevent="onSubmit()">
                <i class="fa fa-save mr-2"></i>Save
            </button>
        </template>
    </SliderModal>
</template>

<script>
import {required, minLength} from "vuelidate/lib/validators";
import SliderModal from "../common/SliderModal";
import DoubtsAPIs from "./DoubtsAPIs";

export default {
    name: "AnswerDoubtsModal",
    components: {
        SliderModal
    },
    props: ["showSlider", "selectedDoubt"],
    data() {
        return {
            answer: "",
        }
    },
    validations: {
        answer: {
            required,
            minLength: minLength(2)
        }
    },
    methods: {
        onClose(status) {
            this.answer = "";
            this.$emit("on-close", status);
        },
        onSubmit() {
            this.$v.$touch();
            if (this.$v.$invalid) {
                return
            }
            const data = {
                answer: this.answer,
                topic_doubt_id: this.selectedDoubt.topic_doubt_id
            };
            $("#answer-doubts-modal").find(".modal").block();
            this.$axios.post(DoubtsAPIs.ANSWER_DOUBTS, data)
                .then(response => {
                    $("#answer-doubts-modal").find(".modal").unblock();
                    const responseData = response.data;
                    if (responseData.success) {
                        $.showNotification("Doubt answer updated successfully", "success");
                        this.onClose(true);
                    } else {
                        $("#answer-doubts-modal").find(".modal").unblock();
                    }
                })
                .catch(() => {
                    $("#answer-doubts-modal").find(".modal").unblock();
                    $.showNotification("Error occurred while sending the data. Try again later", "error");
                });
        },

    }
}
</script>

<style scoped>

</style>
