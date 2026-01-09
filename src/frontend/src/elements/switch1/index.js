import React from 'react';

export default function Switch1(props) {
  const {
    options,
    selectedOption,
    setOption,
    name,
    className
  } = props;

  if (!options?.length) {
    return null;
  }

  const onChange = (value) => {
    setOption(value);
  };

  return (
    <div className={`flex bg-dbm-color-12 rounded-t-lg p-1.5 ${className}`}>
      <div className="flex items-center relative w-full">
        {options.map((option) => {
          const { title } = option;
          const { value } = option;
          const id = `switch_${Math.round(Math.random() * 9999)}`;
          const isChecked = selectedOption === value;

          return (
            // eslint-disable-next-line jsx-a11y/no-noninteractive-element-interactions
            <label
              key={id}
              className={`flex items-center justify-center w-full cursor-pointer uppercase 
              font-bold text-xs px-1.5 py-2 relative z-2
            ${isChecked ? 'text-dbm-color-primary bg-dbm-color-white '
                + 'rounded-md' : 'text-dbm-color-3'}`}
              htmlFor={id}
              onMouseUp={() => { onChange(value); }}
            >
              <input
                className="hidden"
                type="radio"
                id={id}
                name={name}
                value={value}
                onChange={(e) => { onChange(e.target.value); }}
                checked={isChecked}
              />

              <span>{title}</span>
            </label>
          );
        })}
      </div>
    </div>
  );
}
