import React from 'react';
import PropTypes from 'prop-types';
import {
  NotificationsIcon,
  DatabaseIcon,
  UsersIcon,
  ServersIcon,
  NotificationsIconActive,
  DatabaseIconActive,
  UsersIconActive,
  ServersIconActive
} from 'src/elements/icons';

export function IndexOverview(props) {
  const { data } = props;

  if (!data) return;

  const databases = data.databases.paginationInfo.totalCount;
  const servers = data.servers.paginationInfo.totalCount;
  const users = data.users.paginationInfo.totalCount;
  const notifications = data.notifications.paginationInfo.totalCount;

  const dataListObj = [
    {
      title: 'Total database:',
      count: databases,
      icon: !databases ? <DatabaseIcon /> : <DatabaseIconActive />
    },
    {
      title: 'Total servers:',
      count: servers,
      icon: !servers ? <ServersIcon /> : <ServersIconActive />
    },
    {
      title: 'Total users:',
      count: users,
      icon: !users ? <UsersIcon /> : <UsersIconActive />
    },
    {
      title: 'Total notifications:',
      count: notifications,
      icon: !notifications ? <NotificationsIcon /> : <NotificationsIconActive />
    }
  ];

  const dataList = dataListObj.map((item, index) => (
    <div
      key={index}
      className="overview-page-total-card"
    >
      <div className="pl-2 pr-5 flex items-center">
        {item.icon}
      </div>
      <div className="flex flex-col pl-6 border-l-[1px] border-dbm-color-6">
        <span>
          {item.title}
        </span>
        {
          item.count ? (
            <span className="text-2xl text-dbm-color-primary mt-1">
              {item.count}
            </span>
          ) : (
            <span className="text-dbm-color-secondary-dark mt-2">
              not added yet
            </span>
          )
        }
      </div>
    </div>
  ));

  return (
    <div className="text-[14px] font-medium flex">
      {dataList}
    </div>
  );
}

IndexOverview.propTypes = {
  data: PropTypes.object
};
