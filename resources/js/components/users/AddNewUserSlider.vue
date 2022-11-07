<template>
    <SliderModal :show-slider="showSlider" :options="options" @on-close="onClose">
        <template slot="modal-header">
            <h5 class="mt--1rem mb-3">
                User Details
            </h5>
        </template>
        <template slot="modal-content">
            <div class="row mr-2">
                <div class="col-md-12">
                    <div class="form-group form-group-default required" :class="{ 'has-error': $v.name.$error }">
                        <label for="name">User Name</label>
                        <input type="text" class="form-control" name="name" id="name" required
                               placeholder="Ex: Robert Smith" v-model="$v.name.$model">
                    </div>
                    <label class="error" role="alert" for="name"
                           v-if="$v.name.$error&&!$v.name.required">
                        User name is required
                    </label>
                    <label class="error" role="alert" for="name"
                           v-if="$v.name.$error&&!$v.name.minLength">
                        User name length should be at-least 3 letters
                    </label>
                </div>
            </div>
            <div class="row mr-2">
                <div class="col-md-12">
                    <div class="form-group form-group-default required"
                         :class="{ 'has-error': $v.mobileNumber.$error }">
                        <label for="mobile_number">Mobile Number</label>
                        <input type="text" class="form-control" name="mobile_number" id="mobile_number" required
                               placeholder="Enter valid phone number" v-model="$v.mobileNumber.$model">
                    </div>
                    <label class="error" role="alert" for="mobile_number"
                           v-if="$v.mobileNumber.$error&&!$v.mobileNumber.required">
                        User mobile number is required
                    </label>
                    <label class="error" role="alert" for="mobile_number"
                           v-if="$v.mobileNumber.$error&&!$v.mobileNumber.minLength">
                        User mobile number length should be at-least 10 letters
                    </label>
                </div>
            </div>
            <div class="row mr-2">
                <div class="col-md-12">
                    <div class="form-group form-group-default required"
                         :class="{ 'has-error': $v.parentNumber.$error }">
                        <label for="name">Parent Mobile Number</label>
                        <input type="text" class="form-control" name="parent_number" id="parent_number" required
                               placeholder="Enter valid parent phone number" v-model="$v.parentNumber.$model">
                    </div>
                    <label class="error" role="alert" for="parent_number"
                           v-if="$v.parentNumber.$error&&!$v.parentNumber.required">
                        User parent mobile number is required
                    </label>
                    <label class="error" role="alert" for="parent_number"
                           v-if="$v.parentNumber.$error&&!$v.parentNumber.minLength">
                        User parent mobile number length should be at-least 10 letters
                    </label>
                </div>
            </div>
            <div class="row mr-2">
                <div class="col-md-12">
                    <div class="form-group form-group-default required"
                         :class="{ 'has-error': $v.email.$error }">
                        <label for="name">User Email</label>
                        <input type="email" class="form-control" name="email" id="email" required
                               placeholder="Enter valid email" v-model="$v.email.$model">
                    </div>
                    <label class="error" role="alert" for="email"
                           v-if="$v.email.$error&&!$v.email.required">
                        User email is required
                    </label>
                    <label class="error" role="alert" for="parent_number"
                           v-if="$v.email.$error&&!$v.email.minLength">
                        User email length should be at-least 8 letters
                    </label>
                </div>
            </div>
            <div class="row mr-2">
                <div class="col-md-12">
                    <div class="form-group form-group-default required" :class="{ 'has-error': $v.nationality.$error }">
                        <label for="nationality">Nationality</label>
                        <input type="text" class="form-control" name="city" id="nationality" required
                               placeholder="Enter the country name" v-model="$v.nationality.$model">
                    </div>
                    <label class="error" role="alert" for="parent_number"
                           v-if="$v.nationality.$error&&!$v.nationality.required">
                        User parent mobile number is required
                    </label>
                    <label class="error" role="alert" for="parent_number"
                           v-if="$v.nationality.$error&&!$v.nationality.minLength">
                        User parent mobile number length should be at-least 10 letters
                    </label>
                </div>
            </div>
            <div class="row mr-2">
                <div class="col-md-12">
                    <div class="form-group form-group-default required" :class="{ 'has-error': $v.state.$error }">
                        <label for="state">State</label>
                        <input type="text" class="form-control" name="city" id="state" required
                               placeholder="Enter the state name" v-model="$v.state.$model">
                    </div>
                    <label class="error" role="alert" for="parent_number"
                           v-if="$v.state.$error&&!$v.state.required">
                        User state details is required
                    </label>
                    <label class="error" role="alert" for="parent_number"
                           v-if="$v.state.$error&&!$v.state.minLength">
                        User state name length should be at-least 3 letters
                    </label>
                </div>
            </div>
            <div class="row mr-2">
                <div class="col-md-12">
                    <div class="form-group form-group-default required" :class="{ 'has-error': $v.city.$error }">
                        <label for="city">City</label>
                        <input type="text" class="form-control" name="city" id="city" required
                               placeholder="Enter the city name" v-model="$v.city.$model">
                    </div>
                    <label class="error" role="alert" for="parent_number"
                           v-if="$v.city.$error&&!$v.city.required">
                        User city details is required
                    </label>
                    <label class="error" role="alert" for="parent_number"
                           v-if="$v.city.$error&&!$v.city.minLength">
                        User city name length should be at-least 3 letters
                    </label>
                </div>
            </div>
            <div class="row mr-2">
                <div class="col-md-12">
                    <div class="form-group form-group-default required" :class="{ 'has-error': $v.place.$error }">
                        <label for="place">Place</label>
                        <input type="text" class="form-control" name="place" id="place" required
                               placeholder="Enter user place" v-model="$v.place.$model">
                    </div>
                    <label class="error" role="alert" for="parent_number"
                           v-if="$v.place.$error&&!$v.place.required">
                        User place details is required
                    </label>
                    <label class="error" role="alert" for="parent_number"
                           v-if="$v.place.$error&&!$v.place.minLength">
                        User place name length should be at-least 3 letters
                    </label>
                </div>
            </div>
            <div class="row mr-2">
                <div class="col-md-12">
                    <div class="form-group form-group-default required" :class="{ 'has-error': $v.pinCode.$error }">
                        <label for="pin_code">PIN Code</label>
                        <input type="number" class="form-control" name="pin_code" id="pin_code" required
                               placeholder="Enter user PIN code" v-model="$v.pinCode.$model">
                    </div>
                    <label class="error" role="alert" for="parent_number"
                           v-if="$v.pinCode.$error&&!$v.pinCode.required">
                        User pin code details is required
                    </label>
                    <label class="error" role="alert" for="parent_number"
                           v-if="$v.pinCode.$error&&!$v.pinCode.minLength">
                        User pin code length should be at-least 6 digits
                    </label>
                </div>
            </div>
            <div class="row mr-2">
                <div class="col-md-12">
                    <div class="form-group form-group-default required" :class="{ 'has-error': $v.schoolName.$error }">
                        <label for="school">School Name</label>
                        <input type="text" class="form-control" name="school" id="school" required
                               placeholder="Enter user school name" v-model="$v.schoolName.$model">
                    </div>
                    <label class="error" role="alert" for="school"
                           v-if="$v.schoolName.$error&&!$v.schoolName.required">
                        User school name details is required
                    </label>
                    <label class="error" role="alert" for="school"
                           v-if="$v.schoolName.$error&&!$v.schoolName.minLength">
                        User school name length should be at-least 4 letters
                    </label>
                </div>
            </div>
            <div class="row mr-2">
                <div class="col-md-12">
                    <div class="form-group form-group-default required" :class="{ 'has-error': $v.schoolPlace.$error }">
                        <label for="school_place">School Place</label>
                        <input type="text" class="form-control" name="school_place" id="school_place" required
                               placeholder="Enter user school place" v-model="$v.schoolPlace.$model">
                    </div>
                    <label class="error" role="alert" for="school"
                           v-if="$v.schoolPlace.$error&&!$v.schoolPlace.required">
                        User school place details is required
                    </label>
                    <label class="error" role="alert" for="school"
                           v-if="$v.schoolPlace.$error&&!$v.schoolPlace.minLength">
                        User school place length should be at-least 4 letters
                    </label>
                </div>
            </div>
            <div class="row mr-2">
                <div class="col-md-12">
                    <div class="form-group form-group-default">
                        <label for="school_place">Referral Code (Optional)</label>
                        <input type="text" class="form-control" name="referral_code" id="referral_code"
                               placeholder="Enter the referral code if available" v-model="referralCode">
                    </div>
                </div>
            </div>

        </template>
        <template slot="modal-footer">
            <button class="btn  btn-default" type="button" @click.prevent="reset">
                <i class="fa fa-times mr-1"></i> Reset
            </button>
            <button class="btn btn-primary" type="button" @click.prevent="save">
                <i class="fa fa-save mr-1"></i> Save
            </button>
        </template>
    </SliderModal>
</template>

<script>
import _ from "lodash";
import {required, minLength} from "vuelidate/lib/validators";
import SliderModal from "../common/SliderModal";
import UsersAPIS from "./UsersAPIS";

export default {
    name: "AddNewUserSlider",
    components: {SliderModal},
    props: {
        showSlider: {
            required: true,
            default: false
        }
    },
    data() {
        return {
            options: {
                position: "right",
                size: "medium",
            },
            name: "",
            mobileNumber: "",
            parentNumber: "",
            nationality: "",
            state: "",
            city: "",
            place: "",
            pinCode: "",
            schoolName: "",
            schoolPlace: "",
            referralCode: "",
            email: ""
        }
    },
    validations: {
        name: {
            required,
            minLength: minLength(3)
        },
        email: {
            required,
            minLength: minLength(8)
        },
        mobileNumber: {
            required,
            minLength: minLength(10)
        },
        parentNumber: {
            required,
            minLength: minLength(10)
        },
        nationality: {
            required,
            minLength: minLength(3)
        },
        state: {
            required,
            minLength: minLength(3)
        },
        city: {
            required,
            minLength: minLength(3)
        },
        place: {
            required,
            minLength: minLength(3)
        },
        pinCode: {
            required,
            minLength: minLength(6)
        },
        schoolName: {
            required,
            minLength: minLength(4)
        },
        schoolPlace: {
            required,
            minLength: minLength(4)
        },
    },
    methods: {
        onClose(status) {
            this.reset();
            this.$emit("on-close", status);
        },
        reset() {
            this.name = "";
            this.mobileNumber = "";
            this.parentNumber = "";
            this.nationality = "";
            this.state = "";
            this.city = "";
            this.place = "";
            this.email = "";
            this.pinCode = "";
            this.schoolName = "";
            this.schoolPlace = ""
            this.referralCode = "";
            this.$v.$reset();
        },
        save() {
            this.$v.$touch();
            if (this.$v.$invalid) {
                $.showNotification("Fill all required fields with valid data", "error");
                return
            }
            const data = {
                "name": this.name,
                "mobile_number": this.mobileNumber,
                "parent_number": this.parentNumber,
                "nationality": this.nationality,
                "state": this.state,
                "city": this.city,
                "place": this.place,
                "email": this.email,
                "school": this.schoolName,
                "school_place": this.schoolPlace,
                "pin_code": this.pinCode,
                "referral_code": this.referralCode
            };
            this.$axios.post(UsersAPIS.ADD_NEW_USER, data)
                .then(response => {
                    const responseData = response.data;
                    if (responseData.success) {
                        $.showNotification("User added successfully", "success");
                        this.onClose(responseData.data);
                    } else {
                        if (responseData.messages && responseData.messages.length) {
                            _.each(responseData.messages, message => {
                                $.showNotification(message, "error");
                            });
                        } else {
                            $.showNotification(responseData.error, "error");
                        }

                    }
                })
                .catch(() => {
                    $.showNotification("Error occurred while saving details. Try again later", "error");
                })
        }
    }
}
</script>

<style scoped>

</style>
