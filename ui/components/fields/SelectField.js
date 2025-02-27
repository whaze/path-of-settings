import { SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const SelectField = ({ id, label, description, options, value, onChange, error }) => {
    // Format options for SelectControl
    const selectOptions = Object.entries(options).map(([optionValue, optionLabel]) => {
        return {
            value: optionValue,
            label: optionLabel
        };
    });
    
    return (
        <div className="pos-field pos-field-select">
            <SelectControl
                label={label}
                help={error || description}
                value={value || ''}
                options={selectOptions}
                onChange={onChange}
                className={error ? 'has-error' : ''}
            />
        </div>
    );
};

export default SelectField;