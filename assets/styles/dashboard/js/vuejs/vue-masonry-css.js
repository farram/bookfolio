const MasonryBreakpointValue = (mixed, windowWidth) => {
    const valueAsNum = parseInt(mixed);

    if (valueAsNum > -1) {
        return mixed;
    } else if (typeof mixed !== 'object') {
        return 0;
    }

    let matchedBreakpoint = Infinity;
    let matchedValue = mixed.default || 0;

    for (let k in mixed) {
        const breakpoint = parseInt(k);
        const breakpointValRaw = mixed[breakpoint];
        const breakpointVal = parseInt(breakpointValRaw);

        if (isNaN(breakpoint) || isNaN(breakpointVal)) {
            continue;
        }

        const isNewBreakpoint = windowWidth <= breakpoint && breakpoint < matchedBreakpoint;

        if (isNewBreakpoint) {
            matchedBreakpoint = breakpoint;
            matchedValue = breakpointValRaw;
        }
    }

    return matchedValue;
}

let renderCount = 0;
const MasonryComponent = {
    props: {
        tag: {
            type: [String],
            default: 'div'
        },
        cols: {
            type: [Object, Number],
            default: 2
        },
        gutter: {
            type: [Object, Number, String],
            default: 2
        },
        css: {
            type: [Boolean],
            default: true
        }
    },
    data: function () {
        return {
            displayColumns: 2,
            displayGutter: '0px'
        }
    },
    mounted: function () {
        this.reCalculate();

        if (window) {
            window.addEventListener('resize', this.reCalculate);
        }
    },
    beforeDestroy: function () {
        if (window) {
            window.removeEventListener('resize', this.reCalculate);
        }
    },
    methods: {
        reCalculate: function () {
            const windowWidth = (window ? window.innerWidth : null) || Infinity;

            this.reCalculateColumnCount(windowWidth);

            this.reCalculateGutterSize(windowWidth);
        },
        reCalculateGutterSize: function (windowWidth) {
            this.displayGutter = MasonryBreakpointValue(this.gutter, windowWidth);
        },
        reCalculateColumnCount: function (windowWidth) {
            let newColumns = MasonryBreakpointValue(this.cols, windowWidth);

            // final bit of making sure its a correct value
            newColumns = Math.max(1, newColumns * 1 || 0);

            this.displayColumns = newColumns;
        },
        itemsInColumns: function () {
            const currentColumnCount = this.displayColumns;
            const itemsInColumns = new Array(currentColumnCount);
            let items = this.$slots.default || [];
            console.log("itemcount:", items.length);

            // This component does not work with a child <transition-group /> ..yet,
            // so for now we think it may be helpful to ignore and skip it for hopefull future support
            if (items.length === 1 && items[0].componentOptions && items[0].componentOptions.tag == 'transition-group') {
                items = items[0].componentOptions.children;
            }

            for (let i = 0, visibleItemI = 0; i < items.length; i++ , visibleItemI++) {
                // skip vues empty whitespace elements unless first item
                if (!items[i].tag && items[i].text == ' ' && visibleItemI > 0) {
                    visibleItemI--;
                }

                const columnIndex = visibleItemI % currentColumnCount;

                if (!itemsInColumns[columnIndex]) {
                    itemsInColumns[columnIndex] = [];
                }

                itemsInColumns[columnIndex].push(items[i]);
            }

            return itemsInColumns;
        }
    },
    render: function (createElement) {
        renderCount++;
        console.log("render", renderCount);
        const childrenInColumns = this.itemsInColumns();
        const columns = [];
        const gutterSize = parseInt(this.displayGutter) === this.displayGutter * 1 ?
            (this.displayGutter + 'px') : this.displayGutter;

        for (let i = 0; i < childrenInColumns.length; i++) {
            const column = createElement('div', {
                //class: 'my-masonry_column',
                key: i + '-' + childrenInColumns.length,
                style: {
                    boxSizing: 'border-box',
                    backgroundClip: 'padding-box',
                    width: (100 / this.displayColumns) + '%',
                    border: '0 solid transparent',
                    borderLeftWidth: gutterSize
                }
            }, childrenInColumns[i]);

            columns.push(column);
        }

        this.prevColumns = childrenInColumns;

        const wrapper = createElement(
            this.tag, // tag name
            this.css ? {
                //class: 'my-masonry',
                style: {
                    display: ['-webkit-box', '-ms-flexbox', 'flex'],
                    marginLeft: '-' + gutterSize
                }
            } : null,
            columns // array of children
        );

        return wrapper;
    }
};

const Plugin = function () { }

Plugin.install = function (Vue, options) {
    if (Plugin.installed) {
        return;
    }

    Vue.component((options && options.name) || 'masonry', MasonryComponent);
}

if (typeof window !== 'undefined' && window.Vue) {
    window.Vue.use(Plugin);
}

export default Plugin;
