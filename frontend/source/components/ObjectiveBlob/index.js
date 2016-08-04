import { Component, B } from '/components/component.js'
import style from './style.css'
import { fmtDate, fmtUnit, getInitials } from '/application/utils.js'


const MapText = (label, unit) => {

    let output = '';

    switch (label.toLowerCase()) {

        case 'overview':
            if (unit.toLowerCase() == 'units') {
                output = 'Unit uplift';
            }
            if (unit.toLowerCase() == 'gbp') {
                output = 'Sales uplift';
            }
            break;
        case 'acquire new customers':
        case 'launch new product':
            output = 'New customers';
            break;
        case 'grow share of category':
            output = 'Uplift in share of category';
            break;
        case 'grow customer product range (cross sell)':
            output = 'Uplift in cross sell';
            break;
        case 'retain existing customer':
            output = 'Customers retained';
            break;
        case 'grow spend per existing customer':
            output = 'Existing customer spend uplift';
            break;
        case 'grow frequency of shop per customer':
            output = 'Uplift in purchase frequency';
            break;
        case 'grow total category':
            output = 'Category sales uplift';
            break;
        case 'retain new customers (trialists)':
            output = 'Uplift in trialists';
            break;
        case 'retain lapsing customers':
            output = 'Uplift in lapsed customers';
            break;
        case 'grow units per existing customer':
            output = 'Existing customer unit uplift';
            break;
        default:
            output = label;
            break;
    }

    return output;

}

const ObjectiveBlob = ({ label, value, unit }) => {

    if (label.toLowerCase() == 'grow share of category') {
        value = value * 100;
    }

    value = B({ className: style.result_value }, fmtUnit(value, unit))
    
    return B({ className: 'i2c_objective_blob' },
        B({ className: 'i2c_objective_blob_inner'},
            B({ className: 'i2c_objective_title' }, MapText(label, unit)),
            B({ className: 'i2c_objective_value' }, value)
        )
    );
    //return B({ className: style.result_label, key: index }, label, value)
}

export default Component({
  getInitialState () {
    return this.props
  },
  render () {
    const data = this.props

    if (data) {
        return ObjectiveBlob(data)
    } else {
        return B('')
    }
  }
})