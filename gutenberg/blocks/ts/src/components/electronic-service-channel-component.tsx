import React from 'react';
import { wp } from 'wp';
import ElectronicServiceChannelInspectorControls from './electronic-service-channel-inspector-controls';

declare var wp: wp;
const { __ } = wp.i18n;

/**
 * Interface describing component props
 */
interface Props {
  editing: boolean,
  channelId?: string,
  component?: string,
  language: string,
  onComponentChange(component: string) : void,
  onLanguageChange(language: string) : void,
  onChannelIdChange(channelId: string): void
}

/**
 * Interface describing component state
 */
interface State {
}

/**
 * Electronic channel block
 */
class ElectronicServiceChannelComponent extends React.Component<Props, State> {

  /*
   * Constructor
   * 
   * @param props props
   */
  constructor(props: Props) {
    super(props);
    this.state = { };
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
      <ElectronicServiceChannelInspectorControls
        editing={ this.props.editing } 
        channelId={ this.props.channelId }
        language={ this.props.language } 
        component={ this.props. component }
        onComponentChange={ this.props.onComponentChange }
        onLanguageChange={ this.props.onLanguageChange }
        onChannelIdChange={ this.props.onChannelIdChange }/>
    );
  }

  /**
   * Renders preview
   */
  private renderPreview = () => {
    return (
      <div>
        <wp.components.ServerSideRender 
          block="sptv/electronic-service-channel-block" 
          attributes={{
            id: this.props.channelId, 
            language: this.props.language,
            component: this.props.component
          }}/>
      </div>
    );
  }

}
export default ElectronicServiceChannelComponent;