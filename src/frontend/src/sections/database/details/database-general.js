import React from 'react';
import PropTypes from 'prop-types';
import {
  Select,
  MenuItem,
  Divider,
  TextField,
  Typography,
  InputLabel,
  FormControl
} from '@mui/material';
import { format, parseISO } from 'date-fns';
import { useConfig } from 'src/contexts/config-context';
import { usePermission } from 'src/hooks/use-permission';
import { DatabaseGroups } from './database-groups';

export function DatabaseGeneral(props) {
  const { formik, updateDate, database } = props;
  const { databaseStatuses: statuses } = useConfig();
  const { canSee } = usePermission();
  const canEditGroups = canSee('workspace.edit');

  return (
    <div className="card">
      <div className="card-content flex flex-row gap-1 mb-5 justify-between">
        <div>
          <Typography
            className="mb-1"
            variant="h4"
          >
            Database &quot;
            {database.name}
            &quot;
          </Typography>
          <div className="flex flex-row gap-5">
            <div className="sub-heading-0">
              <strong>Engine:</strong>
              <span className="ml-1">{database.engine}</span>
            </div>
            <div className="flex sub-heading-0">
              <strong>Platform:</strong>
              <span className="ml-1">{database.platform}</span>
            </div>
            {(canSee('server.view')) && (
              <div className="sub-heading-0">
                <strong>Server:</strong>
                <span className="ml-1">{database.server.name}</span>
              </div>
            )}
          </div>
        </div>
        <div className="flex flex-row gap-5">
          <div className="flex sub-heading-0">
            Updated at
            <span className="ml-1">{format(parseISO(updateDate), 'dd/MM/yyyy')}</span>
          </div>
        </div>
      </div>

      <Divider className="!mb-5" />

      <div className="card-content flex flex-col gap-5">
        <div className="flex items-start gap-5">
          <TextField
            className="input-0 w-full"
            error={!!(formik.touched.name && formik.errors.name)}
            fullWidth
            helperText={formik.touched.name && formik.errors.name}
            label="Name"
            placeholder="Type Name"
            name="name"
            onBlur={formik.handleBlur}
            onChange={formik.handleChange}
            value={formik.values.name}
          />

          <FormControl
            variant="standard"
            className="select-0 w-full"
            error={!!(formik.touched.status && formik.errors.status)}
          >
            <InputLabel>Status</InputLabel>

            <Select
              label="Status"
              fullWidth
              name="status"
              error={!!(formik.touched.status && formik.errors.status)}
              onChange={formik.handleChange}
              required
              value={formik.values.status}
            >
              {statuses.map((option) => (
                <MenuItem
                  key={option.value}
                  value={option.value}
                >
                  {option.label}
                </MenuItem>
              ))}
            </Select>
          </FormControl>
        </div>
        {canEditGroups && (
          <div className="flex items-start gap-5">
            <DatabaseGroups
              database={database}
              formik={formik}
            />
          </div>
        )}
      </div>
    </div>
  );
}

DatabaseGeneral.propTypes = {
  database: PropTypes.object,
  formik: PropTypes.object,
  updateDate: PropTypes.string
};
