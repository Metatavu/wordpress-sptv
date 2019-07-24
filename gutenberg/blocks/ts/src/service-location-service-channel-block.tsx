import React from 'react';
import { wp, WPBlockTypeEditParams } from 'wp';
import Icon from "./service-location-service-channel-icon";
import ServiceLocationServiceChannelComponent from './components/service-location-service-channel-component';

declare var wp: wp;
const { __ } = wp.i18n;

const { registerBlockType } = wp.blocks;

/**
 * Registers block type
 */
registerBlockType('sptv/service-location-service-channel-block', {
  title: __( 'Service location service channel', 'sptv' ),
  icon: Icon,
  category: 'sptv',

  attributes: {
    "id": {
      type: 'string'
    },
    "component": {
      type: 'string'
    },
    "lang": {
      type: 'string'
    }
  },

  /**
   * Block type edit method 
   */
  edit: ((params: WPBlockTypeEditParams) => {
    const { isSelected } = params;

    return <ServiceLocationServiceChannelComponent 
      editing={ isSelected } 
      channelId={ params.attributes.id } 
      component={ params.attributes.component } 
      lang={ params.attributes.lang }
      onComponentChange={(component: string) => {
        params.setAttributes({"component": component});
      }}
      onLangChange={(lang: string) => {
        params.setAttributes({"lang": lang});
      }}
      onChannelIdChange={(channelId: string) => {
        params.setAttributes({"id": channelId});
      }}
      />
  }),

  /**
   * Block type save method 
   */
  save: (): any => {
    return null;
  }

});

