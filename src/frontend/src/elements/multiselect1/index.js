import React from 'react';
import {
  Select,
  MenuItem, FormHelperText, FormControl
} from '@mui/material';

import CONSTANTS from 'src/elements/multiselect1/Constants';

export default function Multiselect1(props) {
  const {
    options,
    selectedOptions,
    setSelectedOptions,
    className,
    valueKey,
    labelKey,
    topLabel,
    placeholder,
    disabled,
    helperText,
    onChange,
    error
  } = props;

  const optionsLength = options?.length || 0;
  const selectedOptionsLength = selectedOptions?.length || 0;
  const isAllSelected = selectedOptionsLength >= optionsLength;

  const getValuesArray = (arr) => arr.map((arrItem) => arrItem[valueKey]);

  const handleChange = (e) => {
    const { value } = e.target;
    const isSelectAllAction = value.find((selectedOption) => selectedOption === CONSTANTS.selectAll.action);
    const isDeselectAllAction = value.find((selectedOption) => selectedOption === CONSTANTS.deselectAll.action);

    if (isSelectAllAction) {
      setSelectedOptions(getValuesArray(options));

      return;
    }

    if (isDeselectAllAction) {
      setSelectedOptions([]);

      return;
    }

    setSelectedOptions(value);
    e.target.value = value;

    if (onChange) {
      onChange(e);
    }
  };

  return (
    <FormControl className={`${className || ''}`} error={error}>
      <Select
        multiple
        displayEmpty
        className={'multiselect-1'}
        onChange={handleChange}
        error={error}
        value={selectedOptions}
        disabled={disabled}
        renderValue={(selected) => {
          const isEmpty = !selected?.length;

          return (
            <div className="flex flex-col gap-1">
              <div className={`multiselect-1-top-label ${isEmpty ? '!text-sm !text-dbm-color-primary' : ''}`}>
                {isEmpty ? placeholder : topLabel}
              </div>

              {!isEmpty && (
                <ul className="chip-items-1">
                  {selected.map((selectedValue) => {
                    const label = options.find((option) => option[valueKey] === selectedValue)?.[labelKey];

                    return (
                      <div
                        key={selectedValue}
                        className="chip-item-1"
                      >
                        {label}
                      </div>
                    );
                  })}
                </ul>
              )}
            </div>
          );
        }}
      >
        {optionsLength > 1 && (
          <MenuItem
            value={isAllSelected ? CONSTANTS.deselectAll.action : CONSTANTS.selectAll.action}
          >
            {isAllSelected ? CONSTANTS.deselectAll.label : CONSTANTS.selectAll.label}
          </MenuItem>
        )}

        {
          optionsLength
            ? (
              options.map((option) => (
                <MenuItem
                  key={option[valueKey]}
                  value={option[valueKey]}
                >
                  {option[labelKey]}
                </MenuItem>
              ))
            )
            : null
        }
      </Select>
      <FormHelperText>{helperText}</FormHelperText>
    </FormControl>
  );
}
