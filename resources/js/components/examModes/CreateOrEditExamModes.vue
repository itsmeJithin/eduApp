<template>
    <SliderModal id="create-update-class-modal" :show-slider="showSlider" @on-close="onClose">
        <template slot="modal-header">
            <h4 class="mt-0">Create Exam Mode</h4>
        </template>
        <template slot="modal-content">
            <form action="" role="form">
                <div class="form-group" :class="{ 'has-error': $v.modeName.$error }">
                    <label class="required" for="mode-name">Exam Mode Name</label>
                    <input type="text" class="form-control" required name="modeName" id="mode-name"
                           v-model.trim="$v.modeName.$model">
                </div>
                <label class="error" role="alert" for="mode-name"
                       v-if="$v.modeName.$error&&!$v.modeName.required">
                    Enter a valid Exam Mode Name
                </label>
                <label class="error" role="alert" for="mode-name"
                       v-if="$v.modeName.$error&&!$v.modeName.minLength">
                    Exam Mode Name must have at least {{$v.modeName.$params.minLength.min}}
                    letters.
                </label>
                <div class="form-group" :class="{ 'has-error': $v.modeCode.$error }">
                    <label for="mode-code" class="required">Exam Mode Code</label>
                    <input type="text" id="mode-code" class="form-control" required name="modeCode"
                           v-model.trim="$v.modeCode.$model">
                </div>
                <label class="error" role="alert" for="mode-code"
                       v-if="$v.modeCode.$error&&!$v.modeCode.required">
                    Enter a unique Exam Mode Code
                </label>
                <label class="error" role="alert" for="mode-code"
                       v-if="$v.modeCode.$error&&!$v.modeCode.minLength">
                    Exam Mode Code must have at least {{$v.modeCode.$params.minLength.min}}
                    letters.
                </label>

                <div class="form-group">
                    <label for="mode-description">Exam Mode Description</label>
                    <textarea class="form-control" name="mode-description" id="mode-description"
                              v-model.trim="modeDescription">
                    </textarea>
                </div>
            </form>
        </template>
        <template slot="modal-footer">
            <button type="button" class="btn btn-default btn-lg btn-xs-block"
                    @click.prevent="onClose()">
                Cancel
            </button>
            <button type="button" class="btn btn-primary btn-lg btn-xs-block"
                    @click.prevent="onSubmit()">
                {{selectedMode?'Update':'Create'}}
            </button>
        </template>
    </SliderModal>
</template>

<script>
    import SliderModal from "../common/SliderModal";
    import {required, minLength} from "vuelidate/lib/validators";
    import ExamModesAPI from "./ExamModesAPI";

    export default {
        name: "CreateOrEditExamModes",
        components: {
            SliderModal
        },
        props: {
            showSlider: {
                type: Boolean,
                default: false
            },
            selectedMode: {
                default() {
                    return null
                }
            }
        },
        data() {
            return {
                modeName: "",
                modeDescription: "",
                modeCode: ""
            }
        },
        watch: {
            selectedMode() {
                if (this.selectedMode) {
                    this.assignSelectedModeFields();
                }
            }
        },
        validations: {
            modeName: {
                required,
                minLength: minLength(3)
            },
            modeCode: {
                required,
                minLength: minLength(3)
            }
        },
        methods: {
            assignSelectedModeFields() {
                this.modeName = this.selectedMode.exam_mode_name;
                this.modeDescription = this.selectedMode.exam_mode_description;
                this.modeCode = this.selectedMode.exam_mode_code;
            },
            onClose(status) {
                this.reset(status);
                this.$emit("on-close", status);
            },
            reset(status) {
                if (this.selectedMode && !status) {
                    this.assignSelectedModeFields();
                } else {
                    this.modeName = "";
                    this.modeCode = "";
                    this.modeDescription = "";
                    this.$v.$reset();
                }

            },
            onSubmit() {
                this.$v.$touch();
                if (this.$v.$invalid) {
                    return
                }
                const data = {
                    exam_mode_name: this.modeName,
                    exam_mode_code: this.modeCode,
                    exam_mode_description: this.modeDescription,
                };
                if (this.selectedMode) {
                    data.exam_mode_id = this.selectedMode.exam_mode_id;
                }
                this.$axios.post(ExamModesAPI.CREATE_OR_UPDATE_EXAM_MODE, data)
                    .then(response => {
                        const responseData = response.data;
                        if (responseData.success) {
                            this.onClose(true);
                            $.showNotification(responseData.message, "success");
                        } else {
                            $.showNotification(responseData.error, "error");
                        }
                    })
                    .catch(() => {
                        $.showNotification("Error occurred while saving exam mode details. Try again later", "error");
                    })
            }
        }
    }
</script>

<style scoped>

</style>
