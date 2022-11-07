<template>
    <div class="input-group time full-width">
        <input :id="id" type="text" class="form-control" :placeholder="format">
        <span class="input-group-addon"><i class="fa fa-clock-o"></i>
        </span>
    </div>
</template>

<script>
export default {
    name: "TimePicker",
    props: {
        id: {
            type: String,
            default() {
                const randomNo = String(Math.random()).substr(2);
                return "time-picker-".concat(randomNo);
            }
        },
        format: {
            type: String,
            default() {
                return "hh:mm A";
            }
        },
        value: null
    },
    data() {
        return {
            selectedTime: this.value
        }
    },
    watch: {
        value() {
            this.selectedTime = this.value;
        }
    },
    model: {
        event: 'on-change'
    },
    mounted() {
        const self = this;
        self.init();
    },
    methods: {
        init() {
            const self = this;

            $('#'.concat(self.id)).timepicker('setTime', this.value).on('show.timepicker', function (e) {
                var widget = $('.bootstrap-timepicker-widget');
                widget.find('.glyphicon-chevron-up').removeClass().addClass('pg-arrow_maximize');
                widget.find('.glyphicon-chevron-down').removeClass().addClass('pg-arrow_minimize');
            })
                .on("changeTime.timepicker", e => {
                    this.selectedTime = e.time.value;
                    self.$emit("on-change", e.time.value);
                });
        }
    }
}
</script>

<style scoped>

</style>
