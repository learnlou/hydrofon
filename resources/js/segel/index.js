import interact from 'interactjs';
import {debounce} from 'alpinejs/src/utils';
import Grid from './grid';
import Interactions from './interactions';

HYDROFON.Segel = {
    _component: null,
    _element: null,
    initialized: false,
    grid: null,
    size: null,
    interactions: Interactions,
    set component(component) {
        this._component = component;
        this._element = component.el.el;
        this.calculateGrid();
        this.initialized = true;
    },
    get component() {
        return this._component;
    },
    get element() {
        return this._element;
    },
    set resources(resources) {
        this._debounceResources(resources);
    },
    set expanded(categories) {
        this._debounceExpanded(categories);
    },
    _debounceExpanded: debounce(function (expanded) {
        HYDROFON.Segel.component.call('setExpanded', expanded);
    }, 1000),
    _debounceResources: debounce(function (resources) {
        HYDROFON.Segel.component.call('setResources', resources);
    }, 1000),
    calculateGrid: function () {
        this.grid = Grid.create(this.element.clientWidth, 41, this.component.data.steps);
        this.size = {
            min: {
                width: this.element.clientWidth / this.component.data.steps,
                height: 1
            },
            max: {
                width: this.element.clientWidth,
                height: 41
            }
        };
    },
    handleResize: function () {
        this.calculateGrid();

        let bookings = this.element.querySelectorAll('.segel-booking');

        for (const booking of bookings) {
            if (!interact.isSet(booking)) {
                continue;
            }

            let draggable = interact(booking).draggable();
            let resizable = interact(booking).resizable();

            draggable.modifiers[1].options.targets = this.grid;
            resizable.modifiers[2].options.targets = this.grid;
            resizable.modifiers[1].options.min = this.size.min;
            resizable.modifiers[1].options.max = this.size.max;
        }
    }
};
