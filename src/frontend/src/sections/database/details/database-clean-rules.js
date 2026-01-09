import React from 'react';
import PropTypes from 'prop-types';
import {
  TextField,
  MenuItem,
  Alert,
  Select,
  Typography,
  Divider,
  InputLabel,
  FormControl
} from '@mui/material';

export function DatabaseCleanRules(props) {
  const { formik } = props;

  return (
    <div className="card">
      <div className="card-content flex flex-col gap-1 mb-5">
        <Typography
          className="mb-1"
          variant="h4"
        >
          Clean up rules
        </Typography>

        <div className="sub-heading-0">Specify rules for removing old dumps</div>
      </div>

      <Divider className="!mb-5" />

      <div className="card-content mb-2">
        <Typography
          className="mb-1"
          variant="h4"
        >
          Keep backups for
        </Typography>
      </div>

      <div className="card-content flex items-start gap-5 mb-5">
        <TextField
          className="input-0"
          error={!!(formik.touched.cleanUpCount && formik.errors.cleanUpCount)}
          fullWidth
          helperText={formik.touched.cleanUpCount && formik.errors.cleanUpCount}
          label="Count"
          placeholder="Type Count"
          name="cleanUpCount"
          onBlur={formik.handleBlur}
          onChange={formik.handleChange}
          value={formik.values.cleanUpCount}
        />

        <FormControl
          variant="standard"
          className="select-0 w-full"
          error={!!(formik.touched.cleanUpPeriod && formik.errors.cleanUpPeriod)}
        >
          <InputLabel>Period</InputLabel>

          <Select
            name="cleanUpPeriod"
            onBlur={formik.handleBlur}
            error={!!(formik.touched.cleanUpPeriod && formik.errors.cleanUpPeriod)}
            onChange={formik.handleChange}
            value={formik.values.cleanUpPeriod || 'D'}
          >
            <MenuItem value="D">Days</MenuItem>
            <MenuItem value="W">Weeks</MenuItem>
            <MenuItem value="M">Months</MenuItem>
          </Select>
        </FormControl>
      </div>

      <div className="card-content">
        <Alert className="!p-0" severity="warning">Backups that are older that specified period will be removed!</Alert>
      </div>
    </div>
  );
}

DatabaseCleanRules.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  formik: PropTypes.object
};
