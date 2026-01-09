import PropTypes from 'prop-types';
import React, { useState } from 'react';
import {
  Box,
  Button,
  InputLabel,
  Typography,
  Divider,
  TextField,
  MenuItem,
  Alert,
  Select,
  Unstable_Grid2 as Grid,
  FormControl
} from '@mui/material';
import { useMutation } from '@apollo/client';
import { useFormik } from 'formik';
import * as Yup from 'yup';
import { useConfig } from 'src/contexts/config-context';
import { useRouter } from 'next/router';
import { useUrl } from 'src/hooks/use-url';
import Breadcrumbs from 'src/components/breadcrumbs';
import { getUuidFromRelation } from 'src/utils/uuid';
import { UPDATE_SERVER, CREATE_SERVER } from 'src/queries';
import { DeleteServer } from './delete-server';

export function ServerDetails(props) {
  const { data } = props;

  const [showSuccessMessage, setShowSuccessMessage] = useState(false);
  const router = useRouter();
  const url = useUrl();

  const query = data.id ? UPDATE_SERVER : CREATE_SERVER;
  const [saveServer, { saving }] = useMutation(query);
  const { serverStatuses: statuses } = useConfig();

  const formik = useFormik({
    initialValues: {
      name: data.name,
      status: data.status,
      url: data.url,
      ipAddress: data.ipAddress,
    },

    validationSchema: Yup.object({
      name: Yup.string().max(255).required('Name is required'),
      status: Yup.string().max(255).required('Status is required'),
    }),

    onSubmit: async (values, helpers) => {
      const variables = {
        ...values,
      };

      if (data.id) {
        variables.id = data.id;
      }

      try {
        saveServer({ variables }).then((result) => {
          setShowSuccessMessage(true);
          if (!data.id) {
            router.push(
              url.getUrl(`servers/${getUuidFromRelation(result.data.createServer.server.id)}`),
            );
          }
        });
      } catch (err) {
        helpers.setStatus({ success: false });
        helpers.setErrors({ submit: err.message });
        helpers.setSubmitting(false);
      }
    },
  });

  const onStatusChange = (event) => {
    let confirmed = true;
    if (formik.values.status === 'enabled' && event.target.value !== 'enabled') {
      // eslint-disable-next-line no-restricted-globals,no-alert
      confirmed = confirm('This action will disable all active databases! Are you sure?');
    }

    if (confirmed) {
      formik.handleChange(event);
    }
  };

  const redirectToGrid = (time) => {
    router.push(`/servers?reload=${time}`);
  };

  return (
    <form
      autoComplete="off"
      noValidate
      onSubmit={formik.handleSubmit}
    >
      <div className="flex items-end justify-between mb-5">
        <div>
          <Breadcrumbs
            className={'mb-1'}
            collection={[
              { url: url.getUrl('servers'), title: 'Servers' },
              { title: `${data.name ? data.name : 'Create new entity'}` }
            ]}
          />
          <Typography variant="h1">Edit server</Typography>
        </div>
        <div className="flex items-center gap-4">
          <DeleteServer
            uuid={data.id}
            reload={redirectToGrid}
          />
          <Button
            type="submit"
            className="button-7 min-w-[90px]"
            variant="contained"
            disabled={saving}
          >
            Save server
          </Button>
        </div>
      </div>
      {showSuccessMessage && <Alert className="mb-5" severity="success">Server has been saved successfully!</Alert>}
      {
        data.status === 'offline'
        && (
          <Alert className="mb-5" severity="error">
            The server is disabled due to non-activity.
            All databases related to this server are disabled!
          </Alert>
        )
      }
      <div className="card">
        <div className="card-content flex flex-col gap-1 mb-5">
          <Typography
            className="mb-1"
            variant="h4"
          >
            Server &quot;
            {data.name}
            &quot;
          </Typography>

        </div>
        <Divider className="!mb-5" />
        <div className="card-content flex flex-col gap-5">
          <Box sx={{ m: -1.5 }}>
            <Grid
              container
              spacing={3}
            >
              <Grid
                xs={12}
                md={6}
              >
                <TextField
                  error={!!(formik.touched.name && formik.errors.name)}
                  fullWidth
                  className="input-0 w-full"
                  helperText={formik.touched.name && formik.errors.name}
                  label="Name"
                  name="name"
                  onBlur={formik.handleBlur}
                  onChange={formik.handleChange}
                  value={formik.values.name}
                />
              </Grid>
              <Grid
                xs={12}
                md={6}
              >
                <FormControl
                  className="select-0 w-full"
                  variant="standard"
                >
                  <InputLabel>Status</InputLabel>
                  <Select
                    label="Status"
                    fullWidth
                    name="status"
                    error={!!(formik.touched.status && formik.errors.status)}
                    onChange={onStatusChange}
                    required
                    value={formik.values.status}
                  >
                    {statuses
                      && statuses.map((option) => (
                        <MenuItem
                          key={option.value}
                          value={option.value}
                        >
                          {option.label}
                        </MenuItem>
                      ))}
                  </Select>
                </FormControl>
              </Grid>

              <Grid
                xs={12}
                md={6}
              >
                <TextField
                  error={!!(formik.touched.url && formik.errors.url)}
                  fullWidth
                  helperText={formik.touched.url && formik.errors.url}
                  label="Url"
                  className="input-0 w-full"
                  name="url"
                  onBlur={formik.handleBlur}
                  onChange={formik.handleChange}
                  value={formik.values.url}
                />
              </Grid>

              <Grid
                xs={12}
                md={6}
              >
                <TextField
                  error={!!(formik.touched.ipAddress && formik.errors.ipAddress)}
                  fullWidth
                  className="input-0 w-full"
                  helperText={formik.touched.ipAddress && formik.errors.ipAddress}
                  label="IP Address"
                  name="ipAddress"
                  onBlur={formik.handleBlur}
                  onChange={formik.handleChange}
                  value={formik.values.ipAddress}
                />
              </Grid>
            </Grid>
          </Box>
        </div>
      </div>
    </form>
  );
}

ServerDetails.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  data: PropTypes.object,
};
