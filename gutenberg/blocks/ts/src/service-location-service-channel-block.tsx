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
    "language": {
      type: 'string'
    }
  },

  /**
   * Block type edit method 
   */
  edit: ((params: WPBlockTypeEditParams) => {
    const { isSelected } = params;

    const getAttribute = (attribute: string): string => {
      return params.attributes[attribute];
    }

    const setAttribute = (attribute: string, value: string) => {
      const attributes: { [key: string]: string } = { }; 
      attributes[attribute] = value; 
      params.setAttributes(attributes);
    }

    return <ServiceLocationServiceChannelComponent 
      editing={ isSelected } 
      channelId={ getAttribute("id") } 
      component={ getAttribute("component") } 
      language={ getAttribute("language") }
      onComponentChange={(component: string) => {
        setAttribute("component", component);
      }}
      onLanguageChange={(language: string) => {
        setAttribute("language", language);
      }}
      onChannelIdChange={(channelId: string) => {
        setAttribute("id", channelId);
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

