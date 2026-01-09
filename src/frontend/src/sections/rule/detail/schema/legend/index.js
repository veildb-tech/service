import React from 'react';
import constants from 'src/sections/rule/detail/schema/legend/constants';
import { Typography } from '@mui/material';

export function Legend() {
  const { legendItems } = constants;

  return (
    <div className="inline-flex flex-wrap items-center gap-2">
      <Typography
        variant="h5"
        className="text-dbm-color-primary-light"
      >
        Legend:
      </Typography>

      <ul className="flex flex-wrap items-center gap-2">
        {Object.keys(legendItems).map((legendItemKey) => {
          const legendItem = legendItems[legendItemKey];
          const { title } = legendItem;
          const Icon = legendItem.icon({ sx: { fontSize: 20 } });

          return (
            <li
              key={legendItemKey}
              className="flex items-center gap-2"
            >
              {Icon}
              <span className="flex items-center gap-0.5 min-w-max text-dbm-color-primary-light text-sm font-medium
               before:content-[''] before:w-3 before:h-px before:bg-dbm-color-primary-light"
              >
                {title}
              </span>
            </li>
          );
        })}
      </ul>
    </div>
  );
}
