/**
 * Interface describing SPTV ServiceLocationServiceChannelBlock component option
 */
export interface ServiceLocationServiceChannelBlockOptionComponent {
  slug: string,
  name: string
}

/**
 * Interface describing SPTV ServiceLocationServiceChannelBlock options
 */
export interface ServiceLocationServiceChannelBlockOptions {
  components: ServiceLocationServiceChannelBlockOptionComponent[]
}

/**
 * Interface describing SPTV options
 */
export interface SptvOptions {
  serviceLocationServiceChannelBlock: ServiceLocationServiceChannelBlockOptions
}