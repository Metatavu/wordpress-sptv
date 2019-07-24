import React from 'react';
import { wp } from 'wp';
import ServiceLocationServiceChannelInspectorControls from './service-location-service-channel-inspector-controls';

declare var wp: wp;
const { __ } = wp.i18n;

/**
 * Interface describing component props
 */
interface Props {
  editing: boolean,
  channelId?: string,
  component?: string,
  lang: string,
  onComponentChange(component: string) : void,
  onLangChange(lang: string) : void,
  onChannelIdChange(channelId: string): void
}

/**
 * Interface describing component state
 */
interface State {
}

/**
 * Service location block
 */
class ServiceLocationServiceChannelComponent extends React.Component<Props, State> {

  /*
   * Constructor
   * 
   * @param props props
   */
  constructor(props: Props) {
    super(props);
    this.state = {
      version: 0,
      searchOpen: false
    };
  }

  /**
   * Component render method
   */
  public render() {
    return (
      <div>
        { this.renderPreview() }
        { this.renderInspectorControls() }
      </div>
    );
  }

  /**
   * Renders inspector controls
   */
  private renderInspectorControls = () => {
    return (
      <ServiceLocationServiceChannelInspectorControls
        editing={ this.props.editing } 
        channelId={ this.props.channelId }
        language={ this.props.lang } 
        onLanguageChange={ this.props.onLangChange }
        onChannelIdChange={ this.props.onChannelIdChange }/>
    );
  }

  /**
   * Renders preview
   */
  private renderPreview = () => {
    return (
      <div>
        <label>Preview</label>
        <wp.components.ServerSideRender 
          block="sptv/service-location-service-channel-block" 
          attributes={{
            id: this.props.channelId, 
            lang: this.props.lang,
            component: this.props.component
          }}/>
      </div>
    );
  }

}
export default ServiceLocationServiceChannelComponent;