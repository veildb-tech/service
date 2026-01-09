import React from 'react';
import {
  MenuItem, Select, TextField, InputAdornment, InputLabel, FormControl
} from '@mui/material';
import SearchRoundedIcon from '@mui/icons-material/SearchRounded';
import { Legend } from 'src/sections/rule/detail/schema/legend';

export default function Toolbar(props) {
  const {
    setSearchQuery,
    sortOptions,
    setSortOptions,
    getSelectedSortOption
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
    setSortOptions(sortOptions.map((sortOption) => ({ ...sortOption, ...{ isSelected: sortOption.value === e.target.value } })));

  return (
    <div className="flex w-full gap-4 items-center mb-4">
      <TextField
        className="input-1 w-[244px] !min-w-[244px]"
        label="Search by:"
        name="searchquery"
        id="search"
        onChange={onSearchFieldChange}
        InputProps={{
          endAdornment:
  <InputAdornment position="end">
    <SearchRoundedIcon />
  </InputAdornment>,
        }}
      />

      <div className="flex flex-wrap items-center justify-between gap-4 w-full">
        <FormControl
          variant="standard"
          className="select-1"
        >
          <InputLabel>Sort by:</InputLabel>

          <Select
            onChange={onSortFieldChange}
            value={getSelectedSortOption().value}
            label="Sort by:"
          >
            {sortOptions
              && sortOptions.map((sortOption) => (
                <MenuItem
                  key={sortOption.value}
                  value={sortOption.value}
                >
                  {sortOption.label}
                </MenuItem>
              ))}
          </Select>
        </FormControl>

        <Legend />
      </div>

    </div>
  );
}
