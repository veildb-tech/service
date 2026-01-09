import React, { useState } from 'react';
import PropTypes from 'prop-types';
import {
  Table, TableHead, TableRow, TableCell, TableBody
} from '@mui/material';
import {
  useRuleAdditions,
  useRuleAdditionsDispatch,
} from 'src/contexts/rule/rule-additions-context';
import Toolbar from 'src/sections/rule/detail/additional/toolbar';
import { MagentoAttributeRule } from './attribute/rule';

export function MagentoAttributeSetting(props) {
  const { data } = props;
  const dispatch = useRuleAdditionsDispatch();
  const ruleAdditions = useRuleAdditions();
  const [searchQuery, setSearchQuery] = useState('');
  const [searchOptions, setSearchOptions] = useState([
    { value: 'attribute_code', label: 'Attribute', isSelected: true },
    { value: 'backend_type', label: 'Backend Type' },
    { value: 'entity_type_code', label: 'Entity' },
  ]);

  const getUpdatedAdditions = (data) => {
    const attribute = ruleAdditions.find(
      (addition) => addition.attribute_code === data.attribute.attribute_code,
    );
    if (!attribute) {
      return [
        ...ruleAdditions,
        {
          attribute_code: data.attribute.attribute_code,
          entity_type_code: data.attribute.entity_type_code,
          method: data.method,
          value: data.value,
        },
      ];
    }

    return ruleAdditions.map((addition) => {
      if (
        addition.attribute_code === data.attribute.attribute_code
        && addition.entity_type_code === data.attribute.entity_type_code
      ) {
        return {
          ...addition,
          method: data.method,
          value: data.value,
        };
      }
      return addition;
    });
  };

  const handleChange = (attribute, rule) => {
    dispatch({
      type: 'update',
      payload: getUpdatedAdditions({
        attribute,
        value: rule.value,
        method: rule.method,
      }),
    });
  };

  const handleReset = (attribute) => {
    dispatch({
      type: 'update',
      payload: ruleAdditions.filter(
        (addition) =>
          !(
            addition.attribute_code === attribute.attribute_code
            && addition.entity_type_code === attribute.entity_type_code
          ),
      ),
    });
  };

  const getAddition = (attribute) => ruleAdditions.find(
    (addition) =>
      addition.attribute_code === attribute.attribute_code
        && addition.entity_type_code === attribute.entity_type_code,
  );
  const getSelectedSearchOption = () => searchOptions.find((searchOption) => searchOption.isSelected);
  const searchBy = getSelectedSearchOption()?.value;

  return (
    <div className="flex flex-col w-full">
      <Toolbar
        setSearchQuery={setSearchQuery}
        searchOptions={searchOptions}
        setSearchOptions={setSearchOptions}
        getSelectedSearchOption={getSelectedSearchOption}
      />

      <Table
        stickyHeader
        aria-label="sticky table"
        className="table-0"
      >
        <TableHead>
          <TableRow>
            <TableCell>Attribute</TableCell>
            <TableCell>Backend Type</TableCell>
            <TableCell>Type</TableCell>
            <TableCell>Rule</TableCell>
          </TableRow>
        </TableHead>

        <TableBody>
          {data.eav_attributes.map((attribute, index) => {
            const searchColumn = attribute[searchBy];

            if (searchQuery && searchColumn && !searchColumn.includes(searchQuery)) {
              return null;
            }

            return (
              <TableRow
                key={index}
                hover
              >
                <TableCell>{attribute.attribute_code}</TableCell>
                <TableCell>{attribute.backend_type}</TableCell>
                <TableCell>{attribute.entity_type_code}</TableCell>
                <TableCell className="w-[50%]">
                  <MagentoAttributeRule
                    attribute={attribute}
                    onUpdate={handleChange}
                    onReset={handleReset}
                    attributeRule={getAddition(attribute)}
                  />
                </TableCell>
              </TableRow>
            );
          })}
        </TableBody>
      </Table>
    </div>
  );
}

MagentoAttributeSetting.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  data: PropTypes.object,
};
