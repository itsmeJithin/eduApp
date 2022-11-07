<template>
    <div :id = "cardId" class="card card-default" data-pages="card">
        <div class="card-header" :class="{'separator':cardOptions.separator}">
            <div class="card-title">
                <slot name="card-title"></slot>
            </div>
            <div class="card-controls">
                <ul>
                    <li v-if = "cardOptions.edit">
                        <a href="#" @click.stop.prevent = "onEdit()">
                            <i class="pg-icon">edit</i>
                        </a>
                    </li>
                    <li v-if = "cardOptions.delete">
                        <a href="#" @click.stop.prevent = "onDelete()">
                            <i class="pg-icon">trash_alt</i>
                        </a>
                    </li>
                    <li v-if = "cardOptions.settings">
                        <div class="dropdown">
                            <a id="card-settings" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
                                <i class="card-icon card-icon-settings "></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right fs-14 p-1" role="menu" aria-labelledby="card-settings" x-placement="bottom-end">
                                    <a v-for = "(item, index) in cardOptions.settings.items"
                                    :key = "'settings-item-'+index" href="#" 
                                    class="dropdown-item"
                                        @click.stop="settingItemSelected(item.id)">
                                        {{item.label}}
                                    </a>
                            </div>
                        </div>
                    </li>
                    <li v-if = "cardOptions.collapse">
                        <a href="#" class="card-collapse" data-toggle="collapse">
                            <i class="card-icon card-icon-collapse"></i>
                        </a>
                    </li>
                    <li v-if = "cardOptions.refresh">
                        <a href="#" class="card-refresh" data-toggle="refresh">
                            <i class="card-icon card-icon-refresh"></i>
                        </a>
                    </li>
                    <li v-if = "cardOptions.resize">
                        <a href="#" class="card-maximize" data-toggle="maximize">
                            <i class="card-icon card-icon-maximize expandIcon" data-placement="top" :title="cardOptions.expandTooltipMessage"></i>
                        </a>
                    </li>
                    <li v-if = "cardOptions.close">
                        <a href="#" class="card-close" data-toggle="close">
                            <i class="card-icon card-icon-close"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body overflow-auto">
            <slot name="card-content"></slot>
        </div>
    </div>
</template>

<script>
    export default {
        name: "advanced-card",
        components : {},
        props : {
            cardId : {
                type : String,
                default : () => {
                    const dt = new Date().getTime();
                    const id = "card-".concat(dt);
                    return id;
                }
            },
            options : Object
        },
        data () {
            return {
                cardOptions : {
                    edit : false,
                    delete : false,
                    settings : false,
                    expandTooltipMessage : "Expand",
                    collapse : false,
                    refresh : false,
                    resize : false,
                    close : false,
                    separator: false
                }
            }
        },
        mounted () {
            const self = this;
            Object.assign(self.cardOptions, self.options);
            setTimeout(()=> {
                self.init();
            }, 300);
        },
        methods : {
            init () {
                const self = this;
                window.$("#".concat(self.cardId)).card({
                    onRefresh () {
                        self.onRefresh()
                    },
                    onCollapse () {
                        self.onCollapse();
                    },
                    onExpand () {
                        self.onExpand();
                    },
                    onMaximize () {
                        self.onMaximize();
                    },
                    onRestore () {
                        self.onRestore();
                    },
                    onClose () {
                        self.onClose();
                    }
                });
                // window.$(".expandIcon").tooltip('enable')

            },
            onEdit () {
                this.$emit("on-edit");
            },
            onDelete () {
                this.$emit("on-delete");
            },
            onRefresh () {
                this.$emit("on-refresh");
            },
            onCollapse () {

            },
            onExpand () {
                // Refresh while in collapsed state
                this.$emit("on-expand");
            },
            onMaximize () {
                this.$emit("on-maximize");
            },
            onRestore () {
                this.$emit("on-minimize");
            },
            onClose () {
                this.$emit("on-refresh");
            },
            settingItemSelected (id) {
                this.$emit('settings-item-selected',id);
            }
        }
    }
</script>

<style scoped>
    .dropdown-item { 
        opacity: 0.8 !important; 
        margin: 3px 0;
    }
    .dropdown-item:hover { 
        background-color: #F0F0F0 !important; 
    }
</style>