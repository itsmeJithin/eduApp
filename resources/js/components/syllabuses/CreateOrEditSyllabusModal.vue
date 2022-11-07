<template>
    <SliderModal id="create-update-syllabus-modal" :show-slider="showSlider" @on-close="onClose">
        <template slot="modal-header">
            <h4 class="mt-0">Create Syllabus</h4>
        </template>
        <template slot="modal-content">
            <form action="" role="form">
                <div class="form-group" :class="{ 'has-error': $v.syllabusName.$error }">
                    <label for="syllabus-name">Syllabus Name</label>
                    <input type="text" class="form-control" required name="syllabus-name"
                           v-model.trim="$v.syllabusName.$model" id="syllabus-name">
                </div>
                <label class="error" role="alert" for="syllabus-name"
                       v-if="!$v.syllabusName.required">
                    Enter a valid syllabus name
                </label>
                <label class="error" role="alert" for="syllabus-name"
                       v-if="!$v.syllabusName.minLength">
                    Syllabus name must have at least {{$v.syllabusName.$params.minLength.min}}
                    letters.
                </label>
                <div class="form-group" :class="{ 'has-error': $v.syllabusStartYear.$error }">
                    <label>Syllabus Start Year</label>
                    <select class="form-control" required name="syllabus-start-year" id="syllabus-start-year"
                            v-model="$v.syllabusStartYear.$model">
                        <option value="" disabled>Select a start year</option>
                        <option v-for="year in years" :value="year">
                            {{year}}
                        </option>
                    </select>
                </div>
                <label class="error" role="alert" for="syllabus-start-year"
                       v-if="!$v.syllabusStartYear.required">
                    Select a valid syllabus start year
                </label>
                <div class="form-group">
                    <label for="syllabus-end-year">Syllabus Start Year</label>
                    <select class="form-control" required name="syllabus-end-year" id="syllabus-end-year"
                            v-model="$v.syllabusEndYear.$model" :disabled="!endYears.length">
                        <option value="" disabled>Select a end year</option>
                        <option v-for="year in endYears" :value="year">
                            {{year}}
                        </option>
                    </select>
                </div>
                <label class="error" role="alert" for="syllabus-end-year"
                       v-if="!$v.syllabusEndYear.required">
                    Select a valid syllabus end year
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
                {{selectedSyllabus?'Update':'Create'}}
            </button>
        </template>
    </SliderModal>
</template>

<script>
    import _ from "lodash";
    import {required, minLength} from "vuelidate/lib/validators";
    import SliderModal from "../common/SliderModal";
    import SyllabusesAPI from "./SyllabusesAPI";

    export default {
        name: "CreateOrEditSyllabusModal",
        components: {
            SliderModal
        },
        props: ["showSlider", "selectedSyllabus"],
        data() {
            return {
                syllabusName: "",
                syllabusStartYear: "",
                syllabusEndYear: "",
                years: [2021, 2022, 2023, 2024, 2025, 2026],
                endYears: []
            }
        },
        validations: {
            syllabusName: {
                required,
                minLength: minLength(3)
            },
            syllabusStartYear: {
                required
            },
            syllabusEndYear: {
                required
            }
        },
        watch: {
            syllabusStartYear() {
                if (this.syllabusStartYear) {
                    this.endYears = _.slice(this.years, _.indexOf(this.years, this.syllabusStartYear));
                } else {
                    this.syllabusEndYear = "";
                    this.endYears = [];
                }
            },
            selectedSyllabus() {
                if (this.selectedSyllabus) {
                    this.syllabusName = _.clone(this.selectedSyllabus.syllabus_name);
                    this.syllabusStartYear = _.clone(this.selectedSyllabus.start_year);
                    this.endYears = _.slice(this.years, _.indexOf(this.years, this.syllabusStartYear));
                    this.syllabusEndYear = _.clone(this.selectedSyllabus.end_year);
                }
            }
        },
        methods: {
            onClose(status) {
                this.$emit("on-close", status);
                this.syllabusName = "";
                this.syllabusStartYear = "";
                this.syllabusEndYear = "";
            },
            onSubmit() {
                this.$v.$touch();
                if (this.$v.$invalid) {
                    return
                }
                const data = {
                    syllabus_name: this.syllabusName,
                    start_year: this.syllabusStartYear,
                    end_year: this.syllabusEndYear
                };
                if (this.selectedSyllabus) {
                    data.syllabus_id = this.selectedSyllabus.syllabus_id
                }
                $("#create-update-syllabus-modal").block();
                this.$axios.post(SyllabusesAPI.CREATE_OR_UPDATE_SYLLABUS, data)
                    .then(response => {
                        $("#create-update-syllabus-modal").unblock();
                        const responseData = response.data;
                        if (responseData.success) {
                            if (this.selectedSyllabus)
                                $.showNotification("Syllabus details updated successfully", "success");
                            else
                                $.showNotification("Syllabus created successfully", "success");
                            this.onClose(true);
                        } else {
                            $.showNotification(responseData.error, "error");
                        }
                    })
                    .catch(() => {
                        $("#create-update-syllabus-modal").unblock();
                        $.showNotification("Error occurred while sending the data. Try again later");
                    });
            },

        }
    }
</script>

<style scoped>

</style>
