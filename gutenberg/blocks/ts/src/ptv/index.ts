import { V10VmOpenApiElectronicChannel, V10VmOpenApiServiceLocationChannel, V10VmOpenApiWebPageChannel, V10VmOpenApiPrintableFormChannel, V10VmOpenApiPhoneChannel, V10VmOpenApiService } from "./model/v10/api";
import { wp } from "wp";

declare var wp: wp;
type ServiceChannel = V10VmOpenApiElectronicChannel | V10VmOpenApiPhoneChannel | V10VmOpenApiPrintableFormChannel | V10VmOpenApiServiceLocationChannel | V10VmOpenApiWebPageChannel;
type Service = V10VmOpenApiService;
type Organization = V10VmOpenApiService;
const PTV_URL = "https://api.palvelutietovaranto.suomi.fi/api";

/**
 * PTV client
 */
export default class PTV {

  private channelCache: Map<string, ServiceChannel>;
  private serviceCache: Map<string, Service>;
  private organizationCache: Map<string, Organization>
  
  constructor() {
    this.channelCache = new Map();
    this.serviceCache = new Map();
    this.organizationCache = new Map();
  }

  /**
   * Finds a service channel by id
   * 
   * @param id service channel id
   * @returns found service or null if not found
   */
  public findServiceChannel = async (id: string): Promise<ServiceChannel | null> => {
    const channels = await this.findServiceChannels([id]);
    return channels[0] || null;
  }

  /**
   * Finds a service by id
   * 
   * @param id service id
   * @returns found service or null if not found
   */
  public findService = async (id: string): Promise<Service | null> => {
    const services = await this.findServices([id]);
    return services[0] || null;
  }

  /**
   * Finds a service channels by ids
   * 
   * @param ids service channel ids
   * @returns found service or null if not found
   */
  public findServiceChannels = async (ids: string[]): Promise<ServiceChannel[]> => {
    if (!ids || ids.length == 0) {
      return [];
    }

    const cacheMissIds = ids.filter(id => !this.channelCache.has(id));
    if (cacheMissIds.length > 0) {
      const apiFetch = wp.apiFetch;
      const ptvVersion = await apiFetch({ path: "/sptv/version" });
      const result = await fetch(`${PTV_URL}/${ptvVersion}/ServiceChannel/list?guids=${cacheMissIds.join(",")}`, {
        credentials: "omit"
      });

      const channels = await result.json();

      if (channels && Array.isArray(channels) && channels.length > 0) {
        channels.forEach((channel: ServiceChannel) => this.channelCache.set(channel.id, channel));
      }
    }

    return ids.length > 0 ?
      ids.map(id => this.channelCache.get(id)) :
      [];
  }

  /**
   * Finds services by ids
   * 
   * @param ids service id
   * @returns found service or null if not found
   */
  public findServices = async (ids: string[]): Promise<Service[]> => {
    if (!ids || ids.length == 0) {
      return [];
    }

    const cacheMissIds = ids.filter(id => !this.serviceCache.has(id));
    const filterOutIds = ids.filter(id => !(cacheMissIds.indexOf(id) > -1));
    if (cacheMissIds.length > 0 && filterOutIds.length == 0) {
      const apiFetch = wp.apiFetch;
      const ptvVersion = await apiFetch({ path: "/sptv/version" });
      const result = await fetch(`${PTV_URL}/${ptvVersion}/Service/list?guids=${cacheMissIds.join(",")}`, {
        credentials: "omit"
      });

      const services = await result.json();
      
      services.forEach((service: Service) => this.serviceCache.set(service.id, service));
    }

    return ids.map(id => this.serviceCache.get(id));
  }

  /**
   * Finds organizations by ids
   * 
   * @param ids organization ids
   * @returns found organization or null if not found
   */
  public findOrganizations = async (ids: string[]): Promise<Organization[]> => {
    if (!ids || ids.length == 0) {
      return [];
    }

    const cacheMissIds = ids.filter(id => !this.organizationCache.has(id));
    const filterOutIds = ids.filter(id => !(cacheMissIds.indexOf(id) > -1));
    if (cacheMissIds.length > 0 && filterOutIds.length == 0) {
      const apiFetch = wp.apiFetch;
      const ptvVersion = await apiFetch({ path: "/sptv/version" });
      const result = await fetch(`${PTV_URL}/${ptvVersion}/Organization/list?guids=${cacheMissIds.join(",")}`, {
        credentials: "omit"
      });

      const organizations = await result.json();
      
      organizations.forEach((organization: Organization) => this.organizationCache.set(organization.id, organization));
    }
  
      return ids.map(id => this.organizationCache.get(id));
    }
}