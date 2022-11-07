<template>
    <tr :class="{'bg-success-row':month.is_assigned}">
        <td class="v-align-middle">
            {{ index + 1 }}
        </td>
        <td class="v-align-middle">
            {{ month.subscription_month_name }}
        </td>
        <td class="v-align-middle">
            {{ month.price }}
        </td>
        <td class="v-align-middle">
            <input type="text" class="input-sm form-control" :class="{'has-error':$v.amount.$error}"
                   v-model="$v.amount.$model" :placeholder="month.price?'E.g: '+month.price:'E.g: 10'">
        </td>
        <td class="v-align-middle">
            <a href="" class="fs-18 text-success" v-if="!month.is_assigned" @click.prevent="assignMonth">
                <i class="fa fa-plus-circle"></i>
            </a>
            <a href="" class="fs-18 text-danger" v-else @click.prevent="removeMonth">
                <i class="fa fa-minus-circle"></i>
            </a>
        </td>
    </tr>
</template>

<script>
import {required} from "vuelidate/lib/validators";

export default {
    name: "AssignSubscriptionMonthRow",
    props: {
        month: {
            required: true
        },
        index: {
            required: true
        }
    },
    data() {
        return {
            amount: "",
            isError: false
        }
    },
    validations: {
        amount: {
            required
        }
    },
    methods: {
        assignMonth() {
            this.$v.$touch();
            if (this.$v.$invalid) {
                $.showNotification("You should add paid amount or you can add 0 as paid amount", "error");
                return
            }
            this.month.paid_amount = this.amount;
            this.month.is_assigned = true;
        },
        removeMonth() {
            this.month.is_assigned = false;
        }
    }
}
</script>

<style scoped>

</style>
