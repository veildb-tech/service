import React from 'react';
import {
  MenuItem,
  Select,
  TextField,
  FormControl
} from '@mui/material';
import SearchRoundedIcon from '@mui/icons-material/SearchRounded';

export default function Toolbar(props) {
  const {
    setSearchQuery,
    searchOptions,
    setSearchOptions,
    getSelectedSearchOption
  } = props;

  let timer;
  const onSearchFieldChange = (e) => {
    const { value } = e.target;

    timer && clearTimeout(timer);

    timer = setTimeout(() => {
      setSearchQuery(value.trim());
    }, 600);
  };

  const onSortFieldChange = (e) =>
    setSearchOptions(searchOptions.map((searchOption) => ({ ...searchOption, ...{ isSelected: searchOption.value === e.target.value } })));

  return (
    <div className="flex gap-4 items-center mb-3 self-start bg-dbm-color-8 rounded-lg pr-3">
      <TextField
        className="input-1 w-[244px] !min-w-[244px] !bg-transparent"
        label={(
          <div className="flex items-center gap-2">
            <SearchRoundedIcon />
            <span>Search by:</span>
          </div>
        )}
        name="searchquery"
        onChange={onSearchFieldChange}
      />

      <div className="flex flex-wrap items-center justify-between gap-4 w-full">
        <FormControl
          variant="standard"
          className="select-1"
        >
          <Select
            onChange={onSortFieldChange}
            value={getSelectedSearchOption().value}
          >
            {searchOptions
              && searchOptions.map((searchOption) => (
                <MenuItem
                  key={searchOption.value}
                  value={searchOption.value}
                >
                  {searchOption.label}
                </MenuItem>
              ))}
          </Select>
        </FormControl>
      </div>
    </div>
  );
}
