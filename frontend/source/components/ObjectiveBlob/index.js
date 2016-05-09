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
        default:
            output = label;
            break;
         
    }
    
    return output;
    
}

const ObjectiveBlob = ({ label, value, unit }) => {
    
    value = B({ className: style.result_value }, fmtUnit(value, unit))
    return B({ className: 'i2c_objective_blob' }, B({ className: 'i2c_objective_blob_inner'}, B({ className: 'i2c_objective_title' }, MapText(label, unit)  ), B({ className: 'i2c_objective_value' }, value)));
    //return B({ className: style.result_label, key: index }, label, value)
    
}

export default Component({
  getInitialState () {
    return this.props
  },
  render () {
    const data = this.props
    console.log(data)
    if (data) {
        return ObjectiveBlob(data)
    } else {
        return B('')
    }
  }
})