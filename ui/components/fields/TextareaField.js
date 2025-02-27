import { TextareaControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const TextareaField = ({ id, label, description, placeholder, rows, value, onChange, error }) => {
    return (
        <div className="pos-field pos-field-textarea">
            <TextareaControl
                label={label}
                help={error || description}
                value={value || ''}
                onChange={onChange}
                placeholder={placeholder}
                rows={rows || 5}
                className={error ? 'has-error' : ''}
            />
        </div>
    );
};

export default TextareaField;