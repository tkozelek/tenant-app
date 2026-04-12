import './bootstrap';
import noUiSlider from 'nouislider';
import EmblaCarousel from 'embla-carousel';
import { initPriceChart, initVariantPriceChart } from './charts/price-chart.js';
import { initCompareChart } from './charts/compare-chart.js';

window.noUiSlider = noUiSlider;
window.EmblaCarousel = EmblaCarousel;
window.initPriceChart = initPriceChart;
window.initVariantPriceChart = initVariantPriceChart;
window.initCompareChart = initCompareChart;
