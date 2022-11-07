<template>
    <div>
        <ul :class="{'table-pagination':inTable}" class="pagination" v-if="simple">
            <li
                title="Previous"
                class="page-item"
                @click="jumpPage(current-1)"
                :class="{'pagination-disabled':isFirstIndex}">
                <a class="page-link">
                    <i class="pg-icon">chevron_left</i>
                </a>
            </li>
            <li :title="current+'/'+lastIndex" class="pagination-simple-pager">
                <input v-model="pageIndex" size="3"><span class="pagination-slash">／</span>{{ lastIndex }}
            </li>
            <li
                title="Next"
                class="page-item"
                @click="jumpPage(current+1)"
                :class="{'pagination-disabled':isLastIndex}">
                <a class="page-link">
                    <i class="pg-icon">chevron_right</i>
                </a>
            </li>
        </ul>
        <ul :class="{'table-pagination':inTable,'mini':size==='small'}" v-if="!simple" class="pagination">
            <span class="pagination-total-text" v-if="showTotal">{{ total }}</span>
            <li
                title="Previous"
                class="page-item"
                @click="jumpPage(current-1)"
                :class="{'pagination-disabled':isFirstIndex}">
                <a class="page-link">
                    <i class="pg-icon">chevron_left</i>
                </a>
            </li>
            <li
                title="First"
                class="page-item"
                @click="jumpPage(firstIndex)"
                :class="{'active':isFirstIndex}">
                <a class="page-link">{{ firstIndex }}</a>
            </li>
            <li
                @click="jumpBefore(pageSize)"
                class="page-item"
                v-if="(lastIndex >9)&&(current-3>firstIndex)">
                <a class="page-link"><i class="pg-icon">more_horizontal</i></a>
            </li>
            <li v-for="page in pages" :key="'page-'+page.index"
                :title="page.index"
                class="page-item"
                @click="jumpPage(page.index)"
                :class="{'active':current==page.index}">
                <a class="page-link">{{ page.index }}</a>
            </li>
            <li
                @click="jumpAfter(pageSize)"
                class="page-item"
                v-if="(lastIndex >9)&&(current+3<lastIndex)">
                <a class="page-link"><i class="pg-icon">more_horizontal</i></a>
            </li>
            <li
                class="page-item"
                @click="jumpPage(lastIndex)"
                v-if="(lastIndex>0)&&(lastIndex!==firstIndex)"
                :class="{'active':isLastIndex}">
                <a class="page-link">{{ lastIndex }}</a>
            </li>
            <li
                title="Next"
                class="page-item"
                @click="jumpPage(current+1)"
                :class="{'pagination-disabled':isLastIndex}">
                <a class="page-link"><i class="pg-icon">chevron_right</i></a>
            </li>
            <div class="pagination-options">
                <select
                    v-if="showSizeChanger"
                    :size="size=='small'?'small':''"
                    v-model="pageSize">
                    <option v-for="option in options" :key="'option'+option" :value="option">
                        {{ option }}
                    </option>
                    <option v-if="options.indexOf(pageSize)==-1"
                            :value="pageSize">
                        {{ pageSize }}
                    </option>
                </select>
                <div class="pagination-options-quick-jumper"
                     v-if="showQuickJumper">
                    Jump to <input v-model="pageIndex"> Page
                </div>
            </div>
        </ul>
    </div>
</template>

<script>
export default {
    name: "Pagination",
    components: {},
    props: {
        simple: {
            type: Boolean,
            default: false
        },
        inTable: {
            type: Boolean,
            default: false
        },
        showTotal: {
            type: Boolean,
            default: false
        },
        showSizeChanger: {
            type: Boolean,
            default: false
        },
        showQuickJumper: {
            type: Boolean,
            default: false
        },
        size: {
            type: String,
            default: ""
        },
        paginationConf: {
            Type: Object,
            default: () => {
                return {
                    "pageSizeSelectorValues": [10, 20, 30, 40, 50],
                    "pageIndex": 1,
                    "pageSize": 10,
                    "total": 0
                }
            }
        }
    },
    data() {
        return {
            current: 1,
            firstIndex: 1,
            lastIndex: Infinity,
            pages: [],
            pageIndex: 1,
            options: [10, 20, 30, 40, 50],
            total: 0,
            pageSize: 10
        };
    },
    created() {
        this.initPageConf();
    },
    mounted() {
        this.buildIndexes();
    },
    computed: {
        isFirstIndex() {
            return this.current === this.firstIndex;
        },
        isLastIndex() {
            return this.current === this.lastIndex;
        },
        roundPageSize() {
            return Math.round(this.pageSize / 2);
        }
    },
    methods: {
        initPageConf() {
            this.current = this.paginationConf.pageIndex;
            this.options = this.paginationConf.pageSizeSelectorValues.length ? this.paginationConf.pageSizeSelectorValues : this.options;
            this.pageSize = this.paginationConf.pageSize;
            this.total = this.paginationConf.total;
        },
        jumpPage(index) {
            if (index === this.firstIndex - 1 || index === this.lastIndex + 1 || index === this.pageIndex) {
                return;
            }

            if (index < this.firstIndex) {
                this.pageIndex = this.firstIndex;
            } else if (index > this.lastIndex) {
                this.pageIndex = this.lastIndex;
            } else {
                this.pageIndex = index;
            }
        },
        /** generate indexes list */
        buildIndexes() {
            this.lastIndex = Math.ceil(this.total / this.pageSize);
            if (this.current > this.lastIndex) {
                this.pageIndex = this.lastIndex;
            }
            const tmpPages = [];
            if (this.lastIndex <= 9) {
                for (let i = 2; i <= this.lastIndex - 1; i += 1) {
                    tmpPages.push({index: i});
                }
            } else {
                const current = +this.current;
                let left = Math.max(2, current - 2);
                let right = Math.min(current + 2, this.lastIndex - 1);

                if (current - 1 <= 2) {
                    right = 5;
                }

                if (this.lastIndex - current <= 2) {
                    left = this.lastIndex - 4;
                }

                for (let i = left; i <= right; i += 1) {
                    tmpPages.push({index: i});
                }
            }
            this.pages = tmpPages;
        },
        jumpBefore(pageSize) {
            this.jumpPage(this.current - Math.round(pageSize / 2));
        },
        jumpAfter(pageSize) {
            this.jumpPage(this.current + Math.round(pageSize / 2));
        }
    },
    watch: {
        pageIndex() {
            if (this.current === this.pageIndex || this.pageIndex === 0)
                return;
            this.current = this.pageIndex;
            this.$emit("page-index-change", this.pageIndex);
            this.buildIndexes();
        },
        pageSize() {
            this.$emit("page-size-change", this.pageIndex);
            this.buildIndexes();
        },
        paginationConf: {
            handler() {
                this.initPageConf();
                this.buildIndexes();
            },
            deep: true
        }
    }
};
</script>
